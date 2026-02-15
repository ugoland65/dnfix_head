<div style="background-color: #fff;">
    <table class="table-st1">
        <thead>
            <tr>
                <th class="list-idx">고유번호</th>
                <th>수정일</th>
                <th>모드</th>
                <th>요약</th>
                <th>수정자</th>
                <th>수정내용</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($adminActionLogList as $item) {
            ?>
                <tr>
                    <td><?= $item['idx'] ?></td>
                    <td><?= $item['processed_at'] ?></td>
                    <td><?= $item['action_mode'] ?></td>
                    <td><?= $item['action_summary'] ?></td>
                    <td><?= $item['operator_name'] ?></td>
                    <td>

                        <table>
                            <thead>
                                <tr>
                                    <th>필드</th>
                                    <th>이전</th>
                                    <th>이후</th>
                                </tr>
                            </thead>
                            <?php
                                foreach ($item['diff_json'] as $key => $value) {
                            ?>
                                <tr>
                                    <td><?= $key ?></td>
                                    <td><?= $value['before'] ?></td>
                                    <td><?= $value['after'] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>

                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>

<?//= dump($adminActionLogList) ?>