<?php

use App\Controllers\Basecode; // 네임스페이스 포함

$basecode = new Basecode(); 
$basecodeData = $basecode->getBasecode('TreeNode');

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.17/themes/default/style.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.17/themes/default-dark/style.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.17/jstree.min.js" ></script>
<div id="contents_head">
    <h1>BAES CODE3</h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap" class="">

		<div class="category-config-wrap">
		 	<ul>
<?
foreach ($basecodeData['data'] as $row ) {

	$page_title_name[$row['code']] = $row['name'];
?>
	<button type="button" class="btnstyle1  btnstyle1-sm" onclick="location.href='basecode3?group_code=<?=$row['code']?>'"><?=$row['name']?> (<?=$row['code']?>)</button>
<? } ?>
			</ul>
			<ul class="scroll-wrap">

				<div class="tree-header">
					<button class="btnstyle1  btnstyle1-sm" id="addRootNodeBtn">대분류  추가</button>
				</div>

				<div id="jstree"></div>

			</ul>
			<ul class="scroll-wrap">

				<div class="edit-header">
					<ul>
						<h2><?=$page_title_name[$_group_code]?></h2>
					</ul>
					<ul>
						<button type="button" id="" class="btnstyle1 btnstyle1-danger" onclick="categoryCode.cateSave()" >저장</button>
					</ul>
				</div>

				<div id="info_wrap">
					항목명을 선택해 주세요.
				</div>

			</ul>
		</div>

	<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>
<script>

	const group_code = "<?=$_group_code?>";

	const API_ENDPOINTS = {
		procGetTree: "/ad/proc/Category/getTree",
		procCreate: "/ad/proc/Category/createCategory",
		procMod: "/ad/proc/Category/modify",
		procDel: "/ad/proc/Category/delete",
		procDnd: "/ad/proc/Category/dnd",
	};

	const categoryCode = (function() {

		return {

			// 초기화
			init() {
				console.log('categoryCode module initialized.');
			},

			cateInfo( idx ){

				ajaxRequest("/ad/ajax/cate_info", { idx }, { dataType: 'html' })
					.then((getdata) => {
						$('#info_wrap').html(getdata); // 데이터 삽입
					})
					.catch((error) => {
						dnAlert('Error', '에러', 'red');
					});

			},

			//노드생성
			createCategory( depth = "1",  parentKey = "",  parentNodePath = "", name = "" ){

				return ajaxRequest(API_ENDPOINTS.procCreate, {
					'group_code': group_code,
					'depth': depth,
					'parentKey': parentKey,
					'parentNodePath': parentNodePath,
					'name': name,
				})
				.then(res => {
					if (res.status === "success") {
						return res.insert_id; // insert_id 반환
					} else {
						dnAlert('Error', res.message || '상태 변경 실패', 'red');
						throw new Error(res.message || '상태 변경 실패');
					}
				})
				.catch(error => {
					dnAlert('Error', '상태 변경 실패', 'red');
					throw new Error('AJAX 요청 실패');
				});

			},

			//저장
			cateSave( ){
				
				var formData = new FormData($("#cate_info_form")[0]);

				var nameField = formData.get('name');
				var nodeId = formData.get('idx');

				if (!nameField || nameField.trim() === "") {
					dnAlert('Error', '이름 필드는 비어 있을 수 없습니다.', 'red'); // 유효성 검사 실패 시 알림
					return; // 전송 중단
				}

				// 변경된 데이터가 있을 경우 AJAX 요청 실행
				ajaxRequest(API_ENDPOINTS.procMod, formData, { 
					processData: false, 
					contentType: false 
				})
				.done(res => {
					if (res.status === "success") {
						//reloadTree();

						var tree = $('#jstree').jstree(true);
						var node = tree.get_node(nodeId);

						tree.set_text(node, nameField);

						dnAlert('Success!', res.message, 'green');

					} else {
						dnAlert('Error', res.message || '상태 변경 실패', 'red'); // 실패 시 메시지 표시
					}
				})
				.fail(() => {
					dnAlert('Error', '상태 변경 실패', 'red'); // AJAX 요청 실패 시 경고
				});

			},

		}

	})();

$(document).ready(function() {

	$('#jstree').jstree({
		'core': {
			'data': {
				'url': API_ENDPOINTS.procGetTree,
				'type': 'POST',
				'data': function(node) {
					return { 
						'id': node.id === '#' ? 0 : node.id,
						'group_code': group_code 
					};
				},
				'error': function(xhr, status, error) {
					console.error("Failed to load data:", error);
				}
			},
			'themes': {
				'icons': true,          // 아이콘 표시
				'dots': true,           // 점선 활성화
				'stripes': true         // 줄무늬 배경 활성화
			},
			/*
				'name': 'default-dark', // 어두운 테마
				'variant': 'large',     // 큰 텍스트
			*/
			'check_callback': true // 노드 추가, 수정, 삭제 허용
		},
		'contextmenu': {
			'items': function(node) {

				if( node.data == null ){
					var nodeDepth = getDepth(node.id);
					var newDepth = nodeDepth + 1;
					var parentKey = node.id;
					var parentNodePath = '';
				}else{
					var nodeDepth = Number(node.data.depth);
					var newDepth = nodeDepth + 1;
					var parentKey = node.id;
					var parentNodePath = node.data.node_path;
				}

				var tree = $('#jstree').jstree(true);

				return {
					"Create": {
						"label": "하위노드 생성",
						"action": function() {

							// 새 노드 생성
							var newNode = {
								'text': '새분류',
								'data':{ 
									'depth':newDepth,
									'parent_node_path':parentNodePath,
								}
							};
							var newNodeId = tree.create_node(node, newNode);

							if (newNodeId) {
								// 새로 생성된 노드를 선택
								tree.select_node(newNodeId);

								// 약간의 지연 후 편집 모드 시작 (DOM 업데이트 대기)
								setTimeout(function() {
									tree.edit(newNodeId);
								}, 0);
							}

							/*
							categoryCode.createCategory(newDepth, parentKey, parentNodePath)
								.then(insert_id => {

									var newNode = $('#jstree').jstree('create_node', node, { 
										'id' : insert_id,
										'text': '새분류',
										'data':{ 'node_path':parentNodePath + insert_id + '/'  }
									});
									categoryCode.cateInfo(insert_id);
									$('#jstree').jstree('activate_node', newNode);

								})
								.catch(error => {
									console.error("노드 생성 중 오류:", error.message || error);
								});
							*/

						}
					},
					"Delete": {
						"label": "노드 삭제",
						"action": function() {
							
							if ( node.children.length > 0 ) {
								dnAlert('Error', '하위 노드가 있을 경우 삭제가 불가능 합니다.', 'red');
							}else{

								let newPath = parentNodePath.replace(/\/[^\/]+\/$/, '/');

								ajaxRequest(API_ENDPOINTS.procDel, {
									'idx': node.id,
									'group_code': group_code,
									'nodeDepth': nodeDepth,
									'parentNodePath': newPath,
								})
								.then(res => {
									if (res.status === "success") {
										dnAlert('Success!', res.message, 'green');
										$('#jstree').jstree('delete_node', node); // 노드 삭제
									} else {
										dnAlert('Error', res.message || '상태 변경 실패', 'red');
										throw new Error(res.message || '상태 변경 실패');
									}
								})
								.catch(error => {
									dnAlert('Error', '상태 변경 실패', 'red');
									throw new Error('AJAX 요청 실패');
								});

							}

						},
						"_disabled": node.id === "#" // 루트 노드 삭제 비활성화
					}
				};
			}
		},
		'plugins': ['contextmenu','dnd']
	});

	/*
	select_node.jstree: 노드가 선택될 때 발생.
	click.jstree: 노드 텍스트(링크)를 클릭할 때 발생.
	activate_node.jstree: 노드를 활성화할 때 발생.
	changed.jstree: 노드의 선택 상태가 변경될 때 발생.

	$('#jstree').on('select_node.jstree', function (event, data) {
		var node = data.node;
		categoryCode.cateInfo(node.id);
	});
	*/

	$('#jstree').on('click.jstree', '.jstree-anchor', function (event) {
		var node = $('#jstree').jstree(true).get_node(this);
		categoryCode.cateInfo(node.id);
	});

    // 이벤트 핸들링: 노드가 드롭된 후 처리
    $('#jstree').on('move_node.jstree', function(e, data) {

		ajaxRequest(API_ENDPOINTS.procDnd, {
            'node_id': data.node.id,
            'new_parent_id': data.parent,
            'old_parent_id': data.old_parent,
            'new_position': data.position
		})
		.then(res => {
			if (res.status === "success") {
				console.log(res.message);
			} else {
				console.log(res.message);
				dnAlert('Error', res.message, 'red');
			}
		})
		.catch(error => {
			// AJAX 요청 실패 처리
			console.error('AJAX 요청 실패:', error);
			dnAlert('Error', '상태 변경 실패', 'red');
		});

		console.log('드래그 앤 드롭 완료!');
		console.log(e);
		console.log(data);
		console.log('이전 부모:', data.old_parent);
		console.log('새 부모:', data.parent);
		console.log('드롭된 위치:', data.position);

	});

});


	// 대분류 노드 추가
	$("#addRootNodeBtn").click(function() {

		var tree = $('#jstree').jstree(true);

		var newNode = tree.create_node('#', { 
			'text': '새 대분류',
		});

		if (newNode) {
			// 새로 생성된 노드를 선택
			tree.select_node(newNode);

			// 약간의 지연 후 편집 모드 시작 (DOM 업데이트 대기)
			setTimeout(function() {
				tree.edit(newNode);
			}, 0);
		}

		/*
		categoryCode.createCategory()
			.then(insert_id => {
				
				var newNode = $('#jstree').jstree(true).create_node('#', { 
					'id' : insert_id,
					'text': '새분류',
					'data':{ 'node_path': '/' + insert_id + '/'  }
				});
				categoryCode.cateInfo(insert_id);
				$('#jstree').jstree('activate_node', newNode);

			})
			.catch(error => {
				console.error("노드 생성 중 오류:", error);
			});
		*/

	});

	// 이름 변경(편집) 완료 이벤트
	$('#jstree').on('rename_node.jstree', function(e, data) {
		
		console.log(data);
		// 빈 문자열 체크
		if (!data.text || data.text.trim() === '') {
			alert('노드 이름을 입력해주세요.');
			tree.edit(data.node); // 다시 편집 모드로
			return;
		}

		// 새로 생성된 노드인지 확인 (임시 ID를 사용하여 체크)
		var isNewNode = data.node.id.includes('j1_');  // jstree의 임시 ID 형식


		// 새로 생성된 노드였을경우
		if ( isNewNode ){

			//root 노드일경우
			if( data.node.parent == "#" ){
				var newDepth = '1';
				var parentKey = '';
				var parentNodePath = '';
			}else{
				var newDepth = data.node?.data?.depth ?? '';
				var parentKey = data.node?.parent ?? '';
				var parentNodePath = data.node?.data?.parent_node_path ?? '';
			}
			/*
			console.log('newDepth : ', newDepth);
			console.log('parentKey : ', parentKey);
			console.log('parentNodePath : ', parentNodePath);
			return;
			*/

			categoryCode.createCategory(newDepth, parentKey, parentNodePath, data.text)
				.then(insert_id => {

					var tree = $('#jstree').jstree(true);
					tree.set_id(data.node, insert_id);
					//tree.set_data(data.node, { 'node_path':parentNodePath + insert_id + '/'  });
					categoryCode.cateInfo(insert_id);
					$('#jstree').jstree('activate_node', data.node);

				})
				.catch(error => {
					console.error("노드 생성 중 오류:", error.message || error);
				});

		} //if ( isNewNode ){

	});

	//Depth 알아내기
	function getDepth(nodeId) {
		var tree = $('#jstree').jstree(true);
		var path = tree.get_path(nodeId);
		return path.length - 1; // root부터 시작하므로 1을 뺌
	}
</script>
