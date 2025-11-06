<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use App\Controllers\Admin\Basecode;

$basecode = new Basecode(); 

$result = $basecode->basecodeIndex();


?>

<div id="contents_head">
    <h1>BAES CODE</h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap" class="">
		
		<? /*
		<div class="table-grid-top">
			<ul>
				<span class="count">Total : <b></b></span>
			</ul>
			<ul>
				<button type="button" class="btnstyle1  btnstyle1-sm" id="add_row_btn">행 추가</button>
				<button type="button" class="btnstyle1  btnstyle1-sm" id="delete_rows_btn">선택 삭제</button>
				<button type="button" class="btnstyle1  btnstyle1-sm" id="save_rows_btn">저장</button>
			</ul>
		</div>
		*/ ?>

		<div class="table-grid-wrap">
			<ul>

				<button type="button" class="btnstyle1  btnstyle1-sm" onclick="location.href='basecode?cate=BASECODE'">항목관리 (BASECODE)</button>
				<?
				foreach ($result['maincode'] as $row ) {
				?>
					<button type="button" class="btnstyle1  btnstyle1-sm" onclick="location.href='basecode?cate=<?=$row['code']?>'"><?=$row['name']?> (<?=$row['code']?>)</button>
				<? } ?>

			</ul>
			<ul>

				<div class="list-top">
					<ul>
						<span class="count">Total : <b><?=$result['data_total']?></b></span>
					</ul>
					<ul>
						<button type="button" class="btnstyle1  btnstyle1-sm" id="add_row_btn">행 추가</button>
						<button type="button" class="btnstyle1  btnstyle1-sm" id="delete_rows_btn">선택 삭제</button>
						<button type="button" class="btnstyle1  btnstyle1-sm" id="save_rows_btn">저장</button>
					</ul>
				</div>

				<div id="list_new_wrap"></div>
			</ul>
		</div>

<?
	echo "<pre>";
	print_r($result);
	echo "</pre>";
?>
		<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>
<script type="text/javascript"> 
<!-- 


	const API_ENDPOINTS = {
		procDel: "/ad/proc/Admin/Basecode/delete",
		procMod: "/ad/proc/Admin/Basecode/modify",
		procSave: "/ad/proc/Admin/Basecode/saveBasecode",
	};

	const baseCode = (function() {

		return {

			// 초기화
			init() {
				console.log('baseCode module initialized.');
			},

			// 한개저장
			insertSingle( rowData ){

				console.log(rowData);

			},

			// 수정
			modifySingle( idx ) {

				const row = table.getRow(idx); // 특정 idx의 행 가져오기
				const rowData = row.getData(); // 전체 행 데이터 가져오기
				const cells = row.getCells(); // 해당 행의 모든 셀 가져오기

				let hasChanges = false; // 변경 여부 확인용 플래그
				const updatedData = {}; // 변경된 데이터만 저장

				cells.forEach(cell => {
					if (cell.getValue() !== cell.getOldValue()) {
						hasChanges = true;
						updatedData[cell.getField()] = cell.getValue(); // 변경된 데이터만 저장
					}
				});

				if (hasChanges) {

					// 변경된 데이터가 있을 경우 AJAX 요청 실행
					ajaxRequest(API_ENDPOINTS.procMod, {
						idx,
						rowData: rowData 
					})
					.done(res => {
						if (res.status === "success") {
							console.log(res);
							row.update({ Status: null });  // 상태 값을 'U'로 설정
						} else {
							dnAlert('Error', res.message || '상태 변경 실패', 'red'); // 실패 시 메시지 표시
						}
					})
					.fail(() => {
						dnAlert('Error', '상태 변경 실패', 'red'); // AJAX 요청 실패 시 경고
					});

				} else {
					console.log('변경된 데이터가 없습니다.');
				}

			},

			// 삭제
			deleteSingle( idx ) {
				
				dnConfirm(
					'정말 삭제하시겠습니까?',
					'삭제하시면 데이터는 복구되지 않습니다.',
					() => {
						ajaxRequest(API_ENDPOINTS.procDel, {
							idx
						})
							.done(res => {
								if ( res.status == "success" ) {
									dnAlert('Success!', res.message, 'green');
									table.deleteRow(idx); // idx를 기준으로 행 삭제
								} else {
									dnAlert('Error', res.message, 'red');
								}
							})
							.fail(() => dnAlert('Error', '삭제 실패', 'red'));
					}
				);

			},
		
		}	

	})();

	// 초기화 호출
	baseCode.init();



    var addedRows = []; // 새로 추가된 행을 추적하기 위한 배열
    var editedRows = []; // 수정된 행을 추적하기 위한 배열

	var table = new Tabulator("#list_new_wrap", {
		index: "idx",
		layout:"fitDataFill",
		editTriggerEvent:"dblclick", //에디터 더블클릭시 작동
		data:<?=json_encode($result['data'], JSON_UNESCAPED_UNICODE);?>,
		columns:[
			{
				formatter: function (cell, formatterParams) {
					var rowData = cell.getRow().getData();
					
					if (rowData.isNewRow) {
						return ""; // 새 행일 경우 빈 값 반환
					}

					return '<input type="checkbox" class="tabulator-select-row">'; // Tabulator 기본 제공 rowSelection 기능을 모방
				},
				titleFormatter: "rowSelection",  // 헤더에 체크박스 추가
				hozAlign: "center",
				headerHozAlign: "center",
				headerSort: false,
				cellClick: function (e, cell) {
					var checkbox = e.target;
					if (checkbox.classList.contains("tabulator-select-row")) {
						cell.getRow().toggleSelect();  // 체크박스와 선택 상태 연동
					}
				},
			},

			/*
			{ 
				title: "", field:"idx", headerSort: false,
				formatter: function (cell, formatterParams) {
					var rowData = cell.getRow().getData();
					if (rowData.isNewRow) {
						return ""; // 새 행일 경우 빈 값 반환
					}
					return "<button class='btnstyle1 btnstyle1-xs' onclick='agency.openModifyAgencyPopup("+ cell._cell.value +")'>Detail</button>"; 
				},

				cellClick: function(e,c) {
					console.log(e);
					console.log(c._cell.value);
				}, 
			},
			*/

			{
				title:"상태", field:"Status", hozAlign:"center", headerSort: false,
				width:40,
				minWidth:40,
				download:false,
				mutator: function(value, data, type, params) {
					return value || null;  // 값이 없으면 기본값 'I' 반환
				},
				formatter: "lookup",
				formatterParams: {
					"D": "삭제", "I": "입력", "U": "수정", null: ""
				}
			},
			{
				title:"고유번호", field:"idx", hozAlign:"center", headerSort: false, 
				bottomCalc: "count", 
				bottomCalcFormatter: "money", 
				bottomCalcFormatterParams: { symbol: "", precision: 0 },
			},
			{ 
				title:"구분", field:"type", hozAlign:"center", editor:"list", editorParams:{
					values:{
						"Type":"항목"
					}
				}
			},
			{
				title:"이름", field:"name",width: 200 , sorter:"string",editor:true
			},
			{
				title:"코드", field:"code",width: 100 , sorter:"string", editor:true
			},
			{
				title:"메모", field:"memo",width: 200 , sorter:"string", editor:true
			},
			{ 
				title: "관리", field:"",  hozAlign:"center", headerSort: false,
				formatter: function (cell, formatterParams) {
					
					var rowData = cell.getRow().getData();

					if ( rowData.isNewRow ) {
						//var html = "<button class='btnstyle1 btnstyle1-xs' onclick='baseCode.insertSingle("+ cell +")'><i class='fas fa-save'></i></button>"
						var html = ""
							+ " <button class=' delete-single-row-btn btnstyle1 btnstyle1-xs' ><i class='fas fa-trash-alt'></i></button>";
						return html
					}

					var idx = rowData.idx;

					var html = "<button class='btnstyle1 btnstyle1-xs' onclick='baseCode.modifySingle("+ idx +")'><i class='fas fa-save'></i></button>"
						+ " <button class='btnstyle1 btnstyle1-xs' onclick='baseCode.deleteSingle("+ idx +")'><i class='fas fa-trash-alt'></i></button>";

					return html
				},
			},
		],

		//footerElement: "<div id='footer-info' style='text-align: center; padding: 10px;'>총 행 수: 0</div>",

		/*
		페이징
		pagination:"local",
		paginationSize:30,
		movableColumns:true,
		*/

		/*
		rowClick:function(e, row){ //trigger an alert message when the row is clicked
			alert("Row " + row.getData().id + " Clicked!!!!");
		},

		rowDblClick:function(e, row){ //trigger an alert message when the row is clicked
			alert("Row " + row.getData().id + " Clicked!!!!");
		},

		rowDblClickMenu:[
			{
				label:"Delete Row",
				action:function(e, row){
					row.delete();
				}
			},
		],

		movableRows:true,
		rowMoved:function(row){
			alert("Row: " + row.getData().name + " has been moved");
		}
		*/
	});

	table.on("cellEdited", function(cell){

		// cell - 편집된 셀의 컴포넌트
		var row = cell.getRow(); // 해당 셀이 속한 row
		var rowData = row.getData(); // row의 전체 데이터
		var field = cell.getField(); // 편집된 컬럼의 필드명
		var newValue = cell.getValue(); // 편집 후 새로운 값
		var oldValue = cell.getOldValue(); // 편집 전 값
		var edited = cell.isEdited(); // 셀이 편집되었는가?

		if( oldValue == null ) oldValue = "";

		/*
		console.log("셀 편집됨: ", edited);
		console.log("편집된 컬럼: ", field);
		console.log("이전 값: ", oldValue, "새 값: ", newValue);
		console.log("현재 Row 데이터: ", rowData);
		*/

		// 상태 컬럼 업데이트 (Status 필드를 'U'로 설정)
		if ( rowData.Status !== "U" && rowData.Status !== "I" && oldValue != newValue ) {  // 이미 수정 상태가 아닌 경우만 변경
			row.update({ Status: "U" });  // 상태 값을 'U'로 설정
			editedRows.push(row.getData()); // 추가된 행 데이터 저장
		}

	});

	table.on("cellDblClick", function(e, cell){
			//e - the click event object
			//cell - cell component
	});



    // 행 추가 버튼
    document.getElementById("add_row_btn").addEventListener("click", function () {
        table.addRow({
			Status:"I",
			name: "",
			age: "",
			email: "",
			isNewRow: true,
        }).then(function (row) {
            addedRows.push(row.getData()); // 추가된 행 데이터 저장
        });
    });


    // 선택 삭제
	document.getElementById("delete_rows_btn").addEventListener("click", function () {

		const selectedData = table.getSelectedData();

		if (selectedData.length === 0) {
			//alert("선택된 행이 없습니다.");
			dnAlert('Error', '선택된 행이 없습니다.', 'red');
			return;
		}

		// 선택한 행에서 idx만 추출
		const selectedIdx = selectedData.map(row => row.idx);

		dnConfirm(
			'정말 삭제하시겠습니까?',
			'삭제하시면 데이터는 복구되지 않습니다.',
			() => {
				ajaxRequest(API_ENDPOINTS.procDel, {
					idx : selectedIdx
				})
					.done(res => {
						if ( res.status == "success" ) {
							dnAlert('Success!', res.message, 'green');

							// 서버 삭제 성공 시 테이블에서 선택된 행 삭제
							selectedIdx.forEach(idx => {
								table.deleteRow(idx); // idx를 기준으로 행 삭제
							});

						} else {
							dnAlert('Error', res.message, 'red');
						}
					})
					.fail(() => dnAlert('Error', '삭제 실패', 'red'));
			}
		);

    });

    // 추가된 행만 저장하는 버튼
    document.getElementById("save_rows_btn").addEventListener("click", function () {
        
		/*
		if (addedRows.length === 0) {
            alert("추가된 행이 없습니다.");
            return;
        }
		*/

		const requestData = {
			cate: "<?=$_cate?>", 
			rows: addedRows,  // 추가된 행 데이터
			editedRows: editedRows  // 수정된 행 데이터
		};

		// Ajax 요청 및 응답 처리
		ajaxRequest(API_ENDPOINTS.procSave, requestData)
			.done(res => {
				if ( res.status === "success" ) {
					alert(res.message);
					location.reload(); // 페이지 새로고침
				} else {
					alert(`오류: ${res.message}`);
				}
			})
			.fail((xhr, status, error) => {
				console.error("저장 오류:", error);
				alert("저장 중 오류가 발생했습니다. 다시 시도해 주세요.");
			});


    });

	// 테이블 초기화 후 삭제 버튼 이벤트 바인딩
	document.addEventListener("click", function (event) {
		if (event.target.closest(".delete-single-row-btn")) {
			// 클릭한 버튼이 포함된 셀의 행 가져오기
			var rowElement = event.target.closest(".tabulator-row"); // DOM에서 행 요소 가져오기
			var row = table.getRow(rowElement); // Tabulator 행 인스턴스 가져오기

			if (row) {
				row.delete(); // 해당 행 삭제
				//alert("추가된 행이 삭제되었습니다.");
			}
		}
	});






//--> 
</script> 