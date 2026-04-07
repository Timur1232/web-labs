<?php namespace App\Core\View;
use App\Core\Helpers\Log;
use App\Core\Model\Json;
use Closure;
use App\Core\Helpers\Error;
use Exception;
use JsonSerializable;

interface Component {
    function render(): string;
}

final class ComponentFunc implements Component {
    /**
     * @param Closure(): string $comp
     */
    public function __construct(
        public Closure $comp
    ) {}

    /**
     * @param Closure(): string $comp
     */
    public static function from(Closure $comp): self {
        return new self(comp: $comp);
    }

    public function render(): string {
        $comp_fn = $this->comp;
        return $comp_fn();
    }
}

final class TemplateComponent implements Component {

    public static string $template_prefix = 'App/Templates/';

    /*
    * @param array<string, mixed> $data
    */
    public function __construct(
        public string $template_page,
        public array $data = [],
    ) {}

    public function with(string $name, mixed $value): self {
        $this->data[$name] = $value;
        return $this;
    }

    public function render(): string {
        Error::assert(isset($this->template_page), "TemplateComponent: \$template_page must be set.");
        $page_file = self::$template_prefix.$this->template_page.'.php';
        Error::assert(file_exists($page_file), "TemplateComponent: File {$page_file} not exist");
        extract($this->data);
        ob_start();
        include $page_file;
        return ob_get_clean();
    }
}

enum JsonSerialization {
    case DEFAULT;
    case JSON_SERIALIZEABLE;
    case MY_JSON;
}

final class JsonComponent implements Component {
    public function __construct(
        public mixed $object,
        public JsonSerialization $type = JsonSerialization::DEFAULT,
    ) {}
    public function render(): string {
        $json = '';
        switch ($this->type) {
            case JsonSerialization::DEFAULT: {
                if ($this->object instanceof JsonSerializable) {
                    return $this->object->jsonSerialize();
                }
                $json = Json::jsonify($this->object);
            } break;
            case JsonSerialization::MY_JSON: {
                $json = Json::jsonify($this->object);
            } break;
            case JsonSerialization::JSON_SERIALIZEABLE: {
                Error::assert($this->object instanceof JsonSerializable, 'JsonComponent: Serializable object does must implement JsonSerializable interface. Type given: \''.gettype($this->object).'\'');
                $json = $this->object->jsonSerialize();
            } break;
        }
        $json_str = json_encode($json);
        if ($json_str === false) {
            Log::error(__METHOD__.': Unable to serialize object of type '.gettype($this->object));
            Error::internal_error();
        }
        return $json_str;
    }
}
