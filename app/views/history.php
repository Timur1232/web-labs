<section class="content-container">
    <button id="reset-history">Сброс истории</button>
    <table>
        <thead>
            <tr>
                <th colspan="2">История посещений текущего сеанса</th>
                <th colspan="2">Вся история посещений</th>
            </tr>
        </thead>
        <tbody id="history-table">
        </tbody>
    </table>
</section>
<script type="module">
import { displayHistory } from '/public/js/jquery/history.js'
document.addEventListener('DOMContentLoaded', _ => displayHistory());
</script>
