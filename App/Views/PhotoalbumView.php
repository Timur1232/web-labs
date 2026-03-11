<?php

namespace App\Views;

use App\Models\Photoalbum\PhotoalbumModel;

final class PhotoalbumView {
    /**
    * @param PhotoItem[] $photos
    */
    public static function render_imgs(PhotoalbumModel $album): string {
        $str = '';
        foreach ($album->photos as $photo) {
            $str .= <<<HTML
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
        return $str;
    }
}
