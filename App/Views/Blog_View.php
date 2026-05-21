<?php namespace App\Views;
use App\Core\View\Component_Func;
use App\Core\View\View;

final class Blog_View {
    public static function comment_form(int $blog_id): Component_Func {
        return View::func(function () use ($blog_id): string {
            return <<<HTML
                <div id="comment_form">
                    <textarea id="text" rows="5"
                        class="input-text"
                    ></textarea>
                    <button class="button-submit"
                        hx-on:click="send_comment({$blog_id})"
                    >
                        Отправить
                    </button>
                    <button class="button-reset"
                        hx-get="/api/blog/{$blog_id}/button"
                        hx-target="#comment"
                        hx-swap="innerHTML"
                    >Отмена</button>
                </div>
                HTML;
        });
    }

    public static function comment_button(int $blog_id): Component_Func {
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
    public static function comments_html(array $comments): Component_Func {
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
