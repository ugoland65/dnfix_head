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
            $renderDiffValue = static function ($rawValue): void {
                if ($rawValue === null || $rawValue === '') {
                    echo '-';
                    return;
                }

                if (is_array($rawValue) || is_object($rawValue)) {
                    $json = json_encode($rawValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                    echo '<pre style="white-space:pre-wrap; word-break:break-word; margin:0;">' . htmlspecialchars((string)$json, ENT_QUOTES, 'UTF-8') . '</pre>';
                    return;
                }

                if (is_string($rawValue)) {
                    $trimmed = trim($rawValue);
                    if ($trimmed === '') {
                        echo '-';
                        return;
                    }
                    $decoded = json_decode($trimmed, true);
                    if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                        $json = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                        echo '<pre style="white-space:pre-wrap; word-break:break-word; margin:0;">' . htmlspecialchars((string)$json, ENT_QUOTES, 'UTF-8') . '</pre>';
                        return;
                    }
                }

                echo nl2br(htmlspecialchars((string)$rawValue, ENT_QUOTES, 'UTF-8'));
            };

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
                                                $renderDiffValue($beforeValue);
                                                ?>
                                            </details>
                                        <?php } else { ?>
                                            <?php
                                            $beforeValue = $value['before'] ?? null;
                                            $renderDiffValue($beforeValue);
                                            ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($key === 'cd_reg') { ?>
                                            <details>
                                                <summary style="cursor:pointer;">접기/펼치기</summary>
                                                <?php
                                                $afterValue = $value['after'] ?? null;
                                                $renderDiffValue($afterValue);
                                                ?>
                                            </details>
                                        <?php } else { ?>
                                            <?php
                                            $afterValue = $value['after'] ?? null;
                                            $renderDiffValue($afterValue);
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