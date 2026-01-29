<?php

namespace App\Views;
require_once 'app/core/view.php';
require_once 'app/models/photoalbum.php';

use App\Core\View;
use App\Models\PhotoalbumModel;

class PhotoalbumView extends View {

    private function __construct() {
        parent::__construct([], []);
    }

    public static function default(): self {
        return new self();
    }

    /**
    * @param PhotoItem[] $photos
    */
    public static function render_imgs(PhotoalbumModel $album): string {
        ob_start();
        foreach ($album->photos as $photo) {
            echo <<<HTML
            <li class="photo-card shadow">
                <div class="photo-image-container">
                    <img src="/public/media/photo/{$photo->filename}" alt="{$photo->alt}" title="{$photo->title}" />
                </div>
                <div class="photo-label">
                    <p>{$photo->label}</p>
                </div>
            </li>
            HTML;
        }
        return ob_get_clean();
    }
}
