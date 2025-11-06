<?php

use App\Classes\RequestHandler;
use App\Controllers\Category; // 네임스페이스 포함

$requestHandler = new RequestHandler();
$_idx =  $requestHandler->input('idx');

$categoryCode = new Category(); 

$infoData = $categoryCode->getCateInfo();

$data = $infoData['category'];


?>
	<form id="cate_info_form">
	<input type="hidden" name="idx" value="<?=$_idx?>">
	<table class="table-st2 m-t-10">
		<colgroup>
			<col width="15%" />
			<col  />
			<col width="15%" />
			<col  />
		</colgroup>
		<tbody>
			<tr>
				<th>고유번호</th>
				<td>
					<?=$_idx?>
				</td>
				<th></th>
				<td>

				</td>
			</tr>
			<tr>
				<th>분류명</th>
				<td>
					<input class="" name='name' type="text" placeholder="" value='<?=$data['name']?>'>
				</td>
				<th>코드</th>
				<td>
					<input class="" name='code' type="text" placeholder="" value='<?=$data['code']?>'>
				</td>
			</tr>
			<tr>
				<th>메모</th>
				<td colspan="3">
					<input class="" name='memo' type="text" placeholder="" value='<?=$data['memo']?>'>
				</td>
			</tr>
		</tbody>
	</table>
	</form>