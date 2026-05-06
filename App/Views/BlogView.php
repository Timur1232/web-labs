<?php namespace App\Views;
use App\Core\View\ComponentFunc;
use App\Core\View\View;
use App\Models\BlogRecord;

final class BlogView {
    public static function comment_form(int $blog_id): ComponentFunc {
        // TODO: make it work without javascript (with normal forms requests)
        return View::func(function () use ($blog_id): string {
            return <<<HTML
                <form method="post" action="/api/blog/{$blog_id}/comment">
                    <textarea id="text" name="text" rows="5"
                        class="input-text"
                    ></textarea>
                    <button class="button-submit" type="submit">
                        Отправить
                    </button>
                    <button class="button-reset" type="reset"
                        hx-get="/api/blog/{$blog_id}/button"
                        hx-target="#comment"
                        hx-swap="innerHTML"
                    >Отмена</button>
                </form>
                HTML;
        });
    }

    public static function comment_button(int $blog_id): ComponentFunc {
        return View::func(function () use ($blog_id): string {
            return <<<HTML
                <form method="GET" action="/api/blog/{$blog_id}/comment"
                    hx-get="/api/blog/{$blog_id}/comment"
                    hx-target="#comment"
                    hx-swap="innerHTML"
                >
                    <input type="submit" class="button-submit" value="Добавить комментарий"></input>
                </form>
                HTML;
        });
    }
}
