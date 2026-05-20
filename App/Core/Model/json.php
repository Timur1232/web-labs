<?php namespace App\Core\Model;
use App\Core\Helpers\Log;
use Attribute;
use ReflectionClass;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Json {
    public function __construct(
        public ?string $json_name = null,
    ) {}

    public static function jsonify(mixed $obj): ?array {
        $r = new ReflectionClass($obj);
        $fields = $r->getProperties();
        $result = [];
        $count = 0;
        foreach ($fields as $field) {
            $attrs = $field->getAttributes(Json::class);
            if (count($attrs) === 0) {
                continue;
            }
            if ($field->isPrivate() || $field->isProtected()) {
                Log::warning(__METHOD__.": Unable to serialize private or protected property: {$field->getName()}");
                continue;
            }
            $field_name = $field->getName();
            $attr = $attrs[0]->newInstance();
            $result[$attr->json_name ?? $field_name] = $obj->$field_name;
            $count++;
        }
        if ($count === 0) {
            Log::warning(__METHOD__.': Serializable object have no Json attributes on properties. Set $no_attributes parameter to true to serialize any object.');
        }
        return $result;
    }
}
