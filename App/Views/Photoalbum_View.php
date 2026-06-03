<?php namespace App\Views;
use App\Models\Photoalbum_Model;

final class Photoalbum_View {
    public static function render_imgs(Photoalbum_Model $album): string {
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
