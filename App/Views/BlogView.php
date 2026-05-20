<?php namespace App\Views;
use App\Core\View\ComponentFunc;
use App\Core\View\View;
use App\Models\BlogRecord;
use App\Models\CommentRecord;

final class BlogView {
    public static function comment_form(int $blog_id): ComponentFunc {
        return View::func(function () use ($blog_id): string {
            return <<<HTML
                <form method="post" action="/api/blog/{$blog_id}/comment">
                    <textarea id="text" name="text" rows="5"
                        class="input-text"
                    ></textarea>
                    <button class="button-submit" type="submit"
                        onclick="send_comments({$blog_id})"
                    >
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
                <form method="GET" action="/api/blog/{$blog_id}/add_comment"
                    hx-get="/api/blog/{$blog_id}/add_comment"
                    hx-target="#comment"
                    hx-swap="innerHTML"
                >
                    <input type="submit" class="button-submit" value="Добавить комментарий"></input>
                </form>
                HTML;
        });
    }

    /*
    * @param CommentRecord[] $comments
    */
    public static function comments_html(array $comments): ComponentFunc {
        return View::func(function () use ($comments): string {
            $result = '';
            foreach ($comments as $comment) {
                $result .= <<<HTML
                    <h4>{$comment->user_name}</h4>
                    <p>UTC {$comment->format()}</p>
                    <p style="margin-bottom:15px; padding: 5px;">{$comment->text}</p>
                HTML;
            }
            return $result;
        });
    }
}
