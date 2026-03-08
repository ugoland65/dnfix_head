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
                                    <td>
                                        <?php if ($key === 'cd_reg') { ?>
                                            <details>
                                                <summary style="cursor:pointer;">접기/펼치기</summary>
                                                <?php
                                                $beforeValue = $value['before'] ?? null;
                                                $beforeDecoded = is_string($beforeValue) ? json_decode($beforeValue, true) : null;
                                                if (is_string($beforeValue) && json_last_error() === JSON_ERROR_NONE) {
                                                    dump($beforeDecoded);
                                                } else {
                                                    echo $beforeValue;
                                                }
                                                ?>
                                            </details>
                                        <?php } else { ?>
                                            <?php
                                            $beforeValue = $value['before'] ?? null;
                                            $beforeDecoded = is_string($beforeValue) ? json_decode($beforeValue, true) : null;
                                            if (is_string($beforeValue) && json_last_error() === JSON_ERROR_NONE) {
                                                dump($beforeDecoded);
                                            } else {
                                                echo $beforeValue;
                                            }
                                            ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($key === 'cd_reg') { ?>
                                            <details>
                                                <summary style="cursor:pointer;">접기/펼치기</summary>
                                                <?php
                                                $afterValue = $value['after'] ?? null;
                                                $afterDecoded = is_string($afterValue) ? json_decode($afterValue, true) : null;
                                                if (is_string($afterValue) && json_last_error() === JSON_ERROR_NONE) {
                                                    dump($afterDecoded);
                                                } else {
                                                    echo $afterValue;
                                                }
                                                ?>
                                            </details>
                                        <?php } else { ?>
                                            <?php
                                            $afterValue = $value['after'] ?? null;
                                            $afterDecoded = is_string($afterValue) ? json_decode($afterValue, true) : null;
                                            if (is_string($afterValue) && json_last_error() === JSON_ERROR_NONE) {
                                                dump($afterDecoded);
                                            } else {
                                                echo $afterValue;
                                            }
                                            ?>
                                        <?php } ?>
                                    </td>
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