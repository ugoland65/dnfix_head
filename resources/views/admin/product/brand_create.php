<form name='brand_form' id='brand_form' method='post' enctype="multipart/form-data" autocomplete="off">

    <table class="table-style border01 width-full">
        <tr>
            <th style="width:120px">이름(국문)</th>
            <td><input type='text' name='bd_name' id='' size='40' value=""></td>
        </tr>
        <tr>
            <th>이름(영문)</th>
            <td><input type='text' name='bd_name_en' id='' size='40' value=""></td>
        </tr>
        <tr>
            <th>메모</th>
            <td>
                <textarea name="bd_memo" rows="3" style="width:100%; height:80px;"></textarea>
            </td>
        </tr>
        <tr>
            <th>이름 그룹<br><b class="filsu">*필수</b></th>
            <td>
                <div>
                    한글초성 :
                    <select name="bd_name_group" id="bd_name_group" style="width:60px;">
                        <?php
                        // 초성: 단일 자음만 노출 (쌍자음 제외)
                        $koreanInitials = ['', 'ㄱ', 'ㄴ', 'ㄷ', 'ㄹ', 'ㅁ', 'ㅂ', 'ㅅ', 'ㅇ', 'ㅈ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ'];
                        foreach ($koreanInitials as $initial) {
                            $selected = ($brandInfo['BD_NAME_GROUP'] ?? '') === $initial ? 'selected' : '';
                            echo "<option value=\"{$initial}\" {$selected}>{$initial}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="m-t-5">
                    알파벳 초성 :
                    <select name="bd_name_en_group" id="bd_name_en_group" style="width:70px;">
                        <?php
                        $englishInitials = array_merge([''], range('A', 'Z'));
                        foreach ($englishInitials as $initial) {
                            $selected = ($brandInfo['BD_NAME_EN_GROUP'] ?? '') === $initial ? 'selected' : '';
                            echo "<option value=\"{$initial}\" {$selected}>{$initial}</option>";
                        }
                        ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <th>활성</th>
            <td>
                <label><input type="radio" name='bd_active' value="N"> 비활성</label>
                <label><input type="radio" name='bd_active' value="Y"> 활성</label>
            </td>
        </tr>
        <tr>
            <th>검색 리스트 노출</th>
            <td>
                <label><input type="radio" name='bd_list_active' value="N"> 비활성</label>
                <label><input type="radio" name='bd_list_active' value="Y"> 활성</label>
            </td>
        </tr>
        <tr>
            <th>쑈당몰 노출</th>
            <td>
                <label><input type="radio" name='bd_showdang_active' value="N" checked> 비활성</label>
                <label><input type="radio" name='bd_showdang_active' value="Y"> 활성</label>
            </td>
        </tr>

        <tr>
            <th>오나디비 노출</th>
            <td>
                <label><input type="radio" name='bd_onadb_active' value="N" checked> 비노출</label>
                <label><input type="radio" name='bd_onadb_active' value="Y"> 노출</label>
            </td>
        </tr>

        <tr>
            <th>홈페이지</th>
            <td><input type='text' name='bd_domain' value="<?= $data['BD_DOMAIN'] ?>"></td>
        </tr>
        <tr>
            <th>간략소개</th>
            <td><input type='text' name='bd_introduce' value="<?= $data['BD_INTRODUCE'] ?>"></td>
        </tr>
    </table>

</form>

<div class="m-t-10 text-center">
    <button type="button" id="" class="btnstyle1 btnstyle1-primary btnstyle1-lg" onclick="brandInfo.createBrand(this);">브랜드 생성</button>
</div>

<script type="text/javascript">
    var brandInfo = (function() {

        /**
         * 브랜드 생성
         */
        function createBrand(obj) {

            var formData = $("#brand_form").serializeArray();
            
            ajaxRequest("/admin/brand/create", formData, {})
                .then(function(res){
                    if( res.success ){
                        alert(res.message);
                        window.location.reload();
                    }
                })
                .catch(function(err){
                    alert(err.message);
                });
        }

        return {
            createBrand
        };
        
    })();
</script>