<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.3/skin-lion/ui.fancytree.min.css" /> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.3/skin-win8-n/ui.fancytree.min.css"  />

<div id="contents_head">
    <h1>BAES CODE2</h1>
</div>

<div id="contents_body">
	<div id="contents_body_wrap" class="">

		<div class="category-config-wrap">
		 	<ul>
			</ul>
			<ul>
				<button id="addRootNodeBtn">대분류  추가</button>
				<!-- <button id="addNodeBtn">하위 추가</button> -->
				<button id="deleteNodeBtn">선택 삭제</button>

				<div id="tree"></div>

			</ul>
			<ul class="scroll-wrap">

				<div class="edit-header">
					<ul>
						<h2>페이지 이름</h2>
					</ul>
					<ul>
						<button type="button" id="show_type_all" class="btnstyle1 btnstyle1-success btnstyle1-sm" onclick="categoryCode.cateSave()" >저장</button>
					</ul>
				</div>

				<div id="info_wrap">
					로딩중....
				</div>

			</ul>
		</div>

	<div id="contents_body_bottom_padding"></div>

	</div>
</div>
<div id="contents_bottom">
    <div class="pageing-wrap" id="pageing_ajax_show"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.3/jquery.fancytree-all-deps.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.3/jquery.fancytree-all.min.js"  ></script>
<script src="//cdn.jsdelivr.net/npm/ui-contextmenu/jquery.ui-contextmenu.min.js"></script>
<script>

	const group_code = "<?=$_group_code?>";

	const API_ENDPOINTS = {
		procCreate: "/ad/proc/Category/createCategory",
		procGetTree: "/ad/proc/Category/getTree",
		procMod: "/ad/proc/Category/modify",
		procDel: "/ad/proc/Basecode/delete",

		procSave: "/ad/proc/Basecode/saveBasecode",
	};

	let active_idx = null;

	const categoryCode = (function() {

		return {

			// 초기화
			init() {
				console.log('categoryCode module initialized.');
			},

			//노드생성
			createCategory( depth = "1",  parentKey = "",  parentNodePath = "" ){

				return ajaxRequest(API_ENDPOINTS.procCreate, {
					group_code: group_code,
					depth: depth,
					parentKey : parentKey,
					parentNodePath : parentNodePath,
					name: "새분류"
				})
				.then(res => {
					if (res.status === "success") {

						active_idx = res.insert_id;
						//this.cateInfo(  res.insert_id );
						return res.insert_id; // insert_id 반환

					} else {
						dnAlert('Error', res.message || '상태 변경 실패', 'red');
						throw new Error(res.message || '상태 변경 실패');
					}
				})
				.fail(() => {
					dnAlert('Error', '상태 변경 실패', 'red');
					throw new Error('AJAX 요청 실패');
				});

			},

			//
			cateInfo( idx ){

				ajaxRequest("/ad/ajax/cate_info", { idx }, { dataType: 'html' })
					.then((getdata) => {
						$('#info_wrap').html(getdata); // 데이터 삽입
					})
					.catch((error) => {
						dnAlert('Error', '에러', 'red');
					})
					.finally(() => {
						//loading('off', 'white'); // 로딩 해제
					});

			},

			//저장
			cateSave( ){
				
				var formData = new FormData($("#cate_info_form")[0]);

				var nameField = formData.get('name'); // name 필드 값 가져오기

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
						reloadTree();
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

	// 초기화 호출
	categoryCode.init();


    $("#tree").fancytree({
        //extensions: ["dnd","edit"],

		selectMode: 1,  // 1: 단일 선택, 2: 다중 선택, 3: 다중 선택(계층)
		clickFolderMode: 1,  // 1: 확장만, 2: 선택만, 3: 확장과 선택

		extensions: ["dnd"],
		source: {
			url: API_ENDPOINTS.procGetTree, // 트리 데이터 API
			type: "POST",
			data: { 
				group_code: group_code,
			},
			cache: false
		},

		// 레이지 로딩 설정
		lazyLoad: function(event, data) {
			console.log("Lazy loading triggered for node:", data.node);
			data.result = $.ajax({
				url: API_ENDPOINTS.procGetTree, // 서버 요청 URL
				type: "POST",
				data: { 
					parentId: data.node.key, 
					key: data.node.key, 
					group_code: group_code,
					parentNodePath: data.node.data.node_path
				},
				dataType: "json"
			});
		},



		// 클릭 이벤트 핸들러
		click: function(event, data) {
			var node = data.node;
			var targetType = data.targetType;  // 클릭된 요소 타입

			// expander(확장 아이콘)를 클릭한 경우
			if (targetType === 'expander') {
				// 기본 확장/축소 동작 허용
				return true;
			}

			// 제목을 클릭한 경우
			if (targetType === 'title') {
				// 선택 상태만 변경
				node.toggleSelected();
				return false;  // 확장/축소 방지
			}

			// 아이콘을 클릭한 경우
			if (targetType === 'icon') {
				// 아이콘 클릭 시 동작 정의
				return false;  // 확장/축소 방지
			}
		},
    
    // 확장/축소 이벤트 핸들러
    expand: function(event, data) {
      console.log("노드 확장됨:", data.node.title);
    },
    
    collapse: function(event, data) {
      console.log("노드 축소됨:", data.node.title);
    },
    
	/*
    // 커스텀 아이콘 설정 (선택사항)
    icon: function(event, data) {
      if (data.node.isFolder()) {
        return data.node.isExpanded() ? "folder-open" : "folder";
      }
      return "file";  // 기본 파일 아이콘
    },
	*/


/*
    // 클릭 이벤트 핸들러
    click: function(event, data) {
      var node = data.node;
      
      // 선택 상태 토글
      node.toggleSelected();
      
      // 선택된 노드 정보 가져오기
      var selectedNodes = data.tree.getSelectedNodes();
      var selectedKeys = $.map(selectedNodes, function(node) {
        return node.key;
      });
      
	  categoryCode.cateInfo( data.node.key );
      // 선택된 노드 처리
      console.log("Selected nodes:", selectedNodes);
      console.log("Selected keys:", selectedKeys);
      
      return false; // 이벤트 버블링 방지
    },
*/


		// 선택 이벤트 핸들러
		select: function(event, data) {
			// 선택 상태가 변경될 때마다 호출
			var node = data.node;

			if (node.selected) {
				// 노드가 선택되었을 때의 처리
				categoryCode.cateInfo( data.node.key );
				console.log("Node selected:", node.title);
			} else {
				// 노드가 선택 해제되었을 때의 처리
				console.log("Node deselected:", node.title);
			}
		},


		activate: function(event, data) {
			//categoryCode.cateInfo( data.node.key );
            console.log("활성화된 노드:", data.node.title, data.node.key);
        },

        dnd: {
            autoExpandMS: 400,  // 드래그 중 폴더 자동 확장 대기 시간
            draggable: {
                zIndex: 1000,
                scroll: true
            },
            preventVoidMoves: true, // 루트 노드로 드롭 방지
            preventRecursiveMoves: true, // 부모가 자식으로 드롭되는 것을 방지
            dragStart: function(node, data) {
                return true; // 드래그 시작 허용
            },
            dragEnter: function(node, data) {
                return true; // 드롭 가능한 노드로 설정
            },
            dragDrop: function(node, data) {
                data.otherNode.moveTo(node, data.hitMode); // 노드를 이동
            }
        },

		/*
        edit: {
            triggerStart: ["dblclick", "f2"], // 더블 클릭 또는 F2 키로 편집 시작
            beforeEdit: function(event, data) {
                console.log("편집 시작 전:", data.node.title);
            },
            save: function(event, data) {
                console.log("새로운 값:", data.input.val());
                // 서버로 저장하려면 이곳에서 AJAX 호출
                return true; // true를 반환하여 변경 내용 저장
            },
            close: function(event, data) {
                if (data.save) {
                    console.log("수정 완료:", data.node.title);
                } else {
                    console.log("수정 취소");
                }
            }
        },
		*/

    });


	$("#tree").contextmenu({
		delegate: "span.fancytree-title",
		autoFocus: true,
		//	menu: "#options",
		menu: [
			//{title: "Cut", cmd: "cut", uiIcon: "ui-icon-scissors"},
			//{title: "Copy", cmd: "copy", uiIcon: "ui-icon-copy"},
			{title: "하위분류 추가", cmd: "add", uiIcon: "ui-icon-copy"},
			{title: "삭제", cmd: "delete", uiIcon: "ui-icon-trash" },

			{title: "Paste", cmd: "paste", uiIcon: "ui-icon-clipboard", disabled: false },
			{title: "----"},
			{title: "Edit", cmd: "edit", uiIcon: "ui-icon-pencil"  },

			{title: "More", children: [
				{title: "Sub 1", cmd: "sub1"},
				{title: "Sub 2", cmd: "sub1"}
				]}
			],

		beforeOpen: function(event, ui) {
			var node = $.ui.fancytree.getNode(ui.target);
			// Modify menu entries depending on node status
			$("#tree").contextmenu("enableEntry", "paste", node.isFolder());
			// Show/hide single entries
			// $("#tree").contextmenu("showEntry", "cut", false);

			// Activate node on right-click
			node.setActive();
			// Disable tree keyboard handling
			ui.menu.prevKeyboard = node.tree.options.keyboard;
			node.tree.options.keyboard = false;
		},
		close: function(event, ui) {
			// Restore tree keyboard handling
			// console.log("close", event, ui, this)
			// Note: ui is passed since v1.15.0
			var node = $.ui.fancytree.getNode(ui.target);
			node.tree.options.keyboard = ui.menu.prevKeyboard;
			node.setFocus();
		},
		select: function(event, ui) {
			
			var node = $.ui.fancytree.getNode(ui.target);
			var parentKey = node.key; // 부모 노드의 key
			var parentDepth = node.getLevel(); // 부모 노드의 depth (루트 노드는 1)
			var parentNodePath = node.data.node_path; // 부모 노드의 node_path
			
			var newDepth = parentDepth + 1;

			if (ui.cmd === "edit") {
				//node.editStart(); 

			} else if (ui.cmd === "add") {

				categoryCode.createCategory(newDepth, parentKey, parentNodePath)
					.then(insert_id => {

						console.log(insert_id);
						console.log(parentKey);
						//reloadTree();

						reloadNodeAndChildren(parentKey);
						/*
						if (node) {
							node.reloadChildren().done(function() {
								console.log("특정 노드가 성공적으로 다시 로드되었습니다.");
							}).fail(function() {
								console.error("노드 재로딩 실패");
							});
						} else {
							console.error("해당 노드를 찾을 수 없습니다.");
						}
						*/
						/*
						var tree = $("#tree").fancytree("getTree");
						var rootNode = tree.getRootNode();


						var newNode = node.editCreateNode("child", {
							title: "새분류",
							key: insert_id // 서버에서 받은 insert_id를 key로 설정
						});
						*/



						//
						//reloadNodeAndChildren(parentKey);
/*
						addChildAndReload(parentKey,{
							title: "새분류",
							key: insert_id // 서버에서 받은 insert_id를 key로 설정
						});


						newNode.makeVisible(); // 노드가 스크롤에 보이도록 설정
						newNode.setActive(true); // 노드를 활성화
*/

					})
					.catch(error => {
						console.error("노드 생성 중 오류:", error.message || error);
					});

			} else if (ui.cmd === "delete") {
				// 삭제 버튼 클릭 시 동작
				node.remove();
				alert("노드가 삭제되었습니다.");
			}

		}
		
	});

	// 대분류 노드 추가
	$("#addRootNodeBtn").click(function() {
		categoryCode.createCategory()
			.then(insert_id => {
				
				var tree = $("#tree").fancytree("getTree");
				var rootNode = tree.getRootNode();

				var newNode = rootNode.addChildren({
					title: "새분류",
					key: insert_id // 서버에서 받은 insert_id를 key로 설정
				});

				//categoryCode.cateInfo( insert_id );
				newNode.makeVisible(); // 노드가 스크롤에 보이도록 설정
				newNode.setActive(true); // 노드를 활성화

			})
			.catch(error => {
				console.error("노드 생성 중 오류:", error);
			});
	});



    // 노드 추가
    $("#addNodeBtn").click(function() {
        var tree = $("#tree").fancytree("getTree");
        var activeNode = tree.getActiveNode();

        if (activeNode) {
            activeNode.editCreateNode("child", {
                title: "새로운 노드"
            });
        } else {
            alert("노드를 선택해주세요.");
        }
    });

    // 노드 삭제
    $("#deleteNodeBtn").click(function() {
        var tree = $("#tree").fancytree("getTree");
        var activeNode = tree.getActiveNode();

        if (activeNode) {
            activeNode.remove();
        } else {
            alert("삭제할 노드를 선택해주세요.");
        }
    });

	function reloadTree() {
		$("#tree").fancytree("getTree").reload({
			url: API_ENDPOINTS.procGetTree, // 새로 로드할 API
			type: "POST",
			data: {
				group_code: group_code // 필요한 데이터 전달
			},
			cache: false
		});
	}

  // 프로그래매틱하게 노드 확장/축소하는 함수
  function toggleNode(key) {
    var tree = $("#tree").fancytree("getTree");
    var node = tree.getNodeByKey(key);
    if (node) {
      node.toggleExpanded();
    }
  }
  
  // 모든 노드 확장
  function expandAll() {
    $("#tree").fancytree("getRootNode").visit(function(node) {
      node.setExpanded(true);
    });
  }
  
  // 모든 노드 축소
  function collapseAll() {
    $("#tree").fancytree("getRootNode").visit(function(node) {
      node.setExpanded(false);
    });
  }

  // 특정 노드 리로드 함수
  function reloadNode(key) {
    
	var tree = $("#tree").fancytree("getTree");
    var node = tree.getNodeByKey(key);
    
    if (node) {
      // 노드가 존재하면 리로드 실행
      node.resetLazy().then(function() {
        // 리로드 완료 후 노드 확장
        return node.setExpanded();
      }).then(function() {
        console.log("노드 리로드 완료:", node.title);
      });
    }
  }
  
  // 특정 노드와 그 자식들 모두 리로드
function reloadNodeAndChildren(key) {
    var tree = $.ui.fancytree.getTree("#tree"); // 최신 방식으로 트리 가져오기
    var node = tree.getNodeByKey(key);
    
	console.log(node);
	//return false;

    if (node) {
        // 자식 노드 제거 후 다시 로드
        node.reloadChildren().done(function() {
            console.log("노드와 자식들 리로드 완료:", node.title);
        }).fail(function() {
            console.error("노드 리로드 실패:", node.title);
        });
    } else {
        console.error("해당 노드를 찾을 수 없습니다:", key);
    }
}

  
  // 특정 노드에 새로운 자식 노드 추가 후 리로드
  function addChildAndReload(parentKey, newChild) {
    var tree = $("#tree").fancytree("getTree");
    var parentNode = tree.getNodeByKey(parentKey);
    
    if (parentNode) {
      // 새 자식 노드 추가
      var childNode = parentNode.addChild(newChild);
      
      // 부모 노드 리로드
      return reloadNode(parentKey);
    }
  }
  
  // 에러 처리를 포함한 리로드
  function reloadNodeWithErrorHandling(key) {
    var tree = $("#tree").fancytree("getTree");
    var node = tree.getNodeByKey(key);
    
    if (node) {
      node.setStatus('loading');  // 로딩 상태 표시
      
      node.resetLazy().then(function() {
        node.setStatus('ok');  // 성공 상태로 변경
        return node.setExpanded();
      }).catch(function(error) {
        console.error("노드 리로드 실패:", error);
        node.setStatus('error');  // 에러 상태로 변경
        // 사용자에게 에러 알림
        alert("노드 리로드 중 오류가 발생했습니다.");
      });
    }
  }


</script>