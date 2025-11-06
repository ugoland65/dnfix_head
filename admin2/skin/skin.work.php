<?php

use App\Controllers\Work; // 네임스페이스 포함

$work = new Work(); 

$data = $work->getWorkInfo();
?>
<style type="text/css">
.work-reg-wrap{
	display:flex;
	gap:20px;
}

.work-reg-wrap > ul{
	&:nth-child(1){
		width:700px;
	}
	&:nth-child(2){
		flex:1;
	}
}

.unit-wrap{ display:flex; }
.unit-wrap > .unit-box{ background-color:#fff; border:1px solid #ccc; border-radius:5px; padding:5px 10px;  } 

.task-table-wrap{ background-color:#fff; border:1px solid #ccc; }
</style>

<div id="contents_head">
	<h1>업무 리스트</h1>
</div>
<div id="contents_body"  class="partition-body">
	<div id="contents_body_wrap">

		<div class="chat-body-wrap">
			<ul class="chat-body-body">
				
				<?
					foreach ( $data as $key => $val ){
						if (isset($val['children']) && !empty($val['children'])) { 
							
							$_task[] = [
								'name' => $val['name'],
								'class' => 'parents',
							];

							foreach ( $val['children'] as $key2 => $val2 ){
								
								$_task[] = [
									'name' => $val2['name'],
									'class' => 'child',
								];

							}

						}else{

							$_task[] = [
								'name' => $val['name'],
								'class' => 'single',
							];

						}
					}
				?>
				<div class="task-table-wrap">
				<table class="table-task">
					<thead>
						<tr>
							<th>이름</th>		
							<th>진행상태</th>	
							<th>담당자</th>		
							<th>데드라인</th>		
						</tr>
					</thead>
					<tbody>
				<?
				foreach ( $_task as $key => $val ){
				?>
					<? if( $val['class'] == "parents" ){ ?>
						<tr class="<?=$val['class']?>">
							<td colspan="4"><b><?=$val['name']?></b></td>
						</tr>
					<? }else{ ?>
						<tr class="<?=$val['class']?>">
							<td><?=$val['name']?></td>
							<td>시작전</td>
							<td>@담당자</td>
							<td></td>
						</tr>
					<? } ?>
				<? } ?>
					</tbody>
				</table>
				</div>



			</ul>
			<ul class="chat-body-comment">

				<?
				/*
	echo "<pre>";
	print_r($data);
	echo "</pre>";
	*/
				?>

			</ul>
		</div>

		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
	<div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>