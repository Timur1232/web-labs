<section class="content-container">
    <button id="reset-history" onclick="resetHistory()">Сброс истории</button>
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
<script>displayHistory()</script>
