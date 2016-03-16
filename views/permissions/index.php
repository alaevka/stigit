<?php
	$this->title = 'Настройка прав доступа';
?>
<!-- Page Content -->
	<div id="page-content-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-6">
					
					<div class="panel-group" id="accordion-permissions-form-form-left" role="tablist" aria-multiselectable="true">
						
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="permissions-form-left-headingOne">
								<h4 class="panel-title">
									<a role="button" data-toggle="collapse" data-parent="#permissions-form-left-headingOne" href="#collapse-permissions-form-left-One" aria-expanded="false" aria-controls="collapse-permissions-form-left-One">
										Должности
									</a>
								</h4>
							</div>
							<div id="collapse-permissions-form-left-One" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="permissions-form-left-headingOne">
								<div class="panel-body" style="padding: 10px 0;">
								<div style="padding: 0 0 5px 10px;">
									<form role="form" id="search_v_f_shras_form" name="search_v_f_shras_form">  
										<div class="row">
											<div class="col-xs-12">
												<div class="input-group">
													<input type="text" class="form-control" class="form-control" id="search-input-shras" placeholder="Введите текст для поиска.." />
													<div class="input-group-btn">
														<button type="submit" class="btn btn-primary">Найти</button>
													</div><!-- /btn-group -->
													<div class="input-group-btn">
														<button type="reset" class="btn btn-default" onclick="$('#jstree-v_f_shras').jstree(true).search('');">Сбросить</button>
													</div><!-- /btn-group -->
												</div><!-- /input-group -->
											</div><!-- /.col-xs-12 -->
										</div><!-- /.row -->
									</form>
								</div>

								<div id="jstree-v_f_shras"></div>

								<script type="text/javascript">

									function _deleteNode(node, tree) {
										var tree = $("#"+tree).jstree(true);
										tree.delete_node(node);
										$('#jstree-v_f_shras li.inner-node-state').each(function(){
								    		if ($(this).find("i.action_write").length == 0 && $(this).find("i.action_read").length == 0) {
								    			var perm_level = $(this).attr('data-perm-level');
								    			if(perm_level == 0) {
								    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
								    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-close" style="color: #ff0000;"></i> ');
								    			} else if(perm_level == 1) {
								    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
								    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
								    			} else if(perm_level == 2) {
								    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-save" style="color: #1AC94F;"></i> ');
								    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
								    			}
								    		}
								    		
										});
									}
									function _setPermLevel(node_id, level, tree) {
										$.ajax({
								        	type: "POST",
								        	dataType: 'json',
								        	url: "index.php?r=site/setpermlevel",
								        	data: "permission_id="+node_id+"&level="+level,
								        	success: function(data_ajax, status){
								        		$('#'+tree).jstree('get_node', node_id).li_attr['data-perm-level'] = level;
								        		$('#'+tree).jstree(true).refresh_node(node_id);

								        		var li_node_id = $('#'+tree).jstree('get_node', node_id).id;
								        		
								        		if(level == 0) {
								        			$('#'+tree).find('#'+li_node_id).find('.action_read').css('color', "#ff0000").removeClass("glyphicon-eye-open").addClass("glyphicon-eye-close");
								        			$('#'+tree).find('#'+li_node_id).find('.action_write').css('color', "#ff0000").removeClass("glyphicon-floppy-save").addClass("glyphicon-floppy-remove");
								    			} else if(level == 1) {
								    				$('#'+tree).find('#'+li_node_id).find('.action_read').css('color', "#1AC94F").removeClass("glyphicon-eye-close").addClass("glyphicon-eye-open");
								        			$('#'+tree).find('#'+li_node_id).find('.action_write').css('color', "#ff0000").removeClass("glyphicon-floppy-save").addClass("glyphicon-floppy-remove");
								    			} else if(level == 2) {
								    				$('#'+tree).find('#'+li_node_id).find('.action_read').css('color', "#1AC94F").removeClass("glyphicon-eye-close").addClass("glyphicon-eye-open");
								        			$('#'+tree).find('#'+li_node_id).find('.action_write').css('color', "#1AC94F").removeClass("glyphicon-floppy-remove").addClass("glyphicon-floppy-save");
								    			}

								        	}
								        });
									}

									$("#search_v_f_shras_form").submit(function(e) {
									  	e.preventDefault();
									  	$("#jstree-v_f_shras").jstree(true).search($("#search-input-shras").val());
									});
									// $(document).ready(function(){
									// 	$("#search-input-shras").keyup(function(){
									// 		var searchString = $(this).val();
									// 		$('#jstree-v_f_shras').jstree('search', searchString);
									// 	});	

									// });
									
									

									$("#jstree-v_f_shras").jstree({
										"dnd" : {
								            "is_draggable" : false,
								        }, 
								        "contextmenu": {
									        "items": function ($node) {
									        	var tree = $("#jstree-v_f_shras").jstree(true);
									        	if($node.li_attr['data-perm-level'] == 0) {
									        		return {
										            	"Read": {
										                    "label": "Разрешить чтение",
										                    "icon " : 'read-icon',
										                    "_disabled" : function (obj) { 
										                    	if($node.parent == '#') {
											                    	return true;
											                    } else {
											                    	return false;
											                    }

											                },
										                    "action": function (obj) {
																_setPermLevel($node.id, 1, 'jstree-v_f_shras');							                        
										                    }
										                },
										                "Write": {
										                    "label": "Разрешить запись",
										                    "icon " : 'write-icon',
										                    "_disabled" : function (obj) { 
										                    	if($node.parent == '#') {
											                    	return true;
											                    } else {
											                    	return false;
											                    }

											                },
										                    "action": function (obj) {
																_setPermLevel($node.id, 2, 'jstree-v_f_shras');												                        
										                    }
										                },
										                "Delete": {
										                    "label": "Удалить",
										                    "icon " : 'delete-icon',
										                    "_disabled" : function (obj) { 
										                    	if($node.parent == '#') {
											                    	return true;
											                    } else {
											                    	return false;
											                    }

											                },
										                    "action": function (obj) {
										            			
										            			$.ajax({
														        	type: "POST",
														        	dataType: 'json',
														        	url: "index.php?r=site/deletepermissions",
														        	data: "permission_id="+$node.id,
														        	success: function(data_ajax,status){
														        		_deleteNode($node, 'jstree-v_f_shras');
														        		
														        	}
														        });
										                        
										                    }
										                }

										            };
									        	} else if($node.li_attr['data-perm-level'] == 1) {
									        		return {
										            	"Read": {
										                    "label": "Запретить чтение",
										                    "icon " : 'read-icon',
										                    "_disabled" : function (obj) { 
										                    	if($node.parent == '#') {
											                    	return true;
											                    } else {
											                    	return false;
											                    }

											                },
										                    "action": function (obj) {
																_setPermLevel($node.id, 0, 'jstree-v_f_shras');				                        
										                    }
										                },
										                "Write": {
										                    "label": "Разрешить запись",
										                    "icon " : 'write-icon',
										                    "_disabled" : function (obj) { 
										                    	if($node.parent == '#') {
											                    	return true;
											                    } else {
											                    	return false;
											                    }

											                },
										                    "action": function (obj) {
																_setPermLevel($node.id, 2, 'jstree-v_f_shras');											                        
										                    }
										                },
										                "Delete": {
										                    "label": "Удалить",
										                    "icon " : 'delete-icon',
										                    "_disabled" : function (obj) { 
										                    	if($node.parent == '#') {
											                    	return true;
											                    } else {
											                    	return false;
											                    }

											                },
										                    "action": function (obj) {
										            			
										            			$.ajax({
														        	type: "POST",
														        	dataType: 'json',
														        	url: "index.php?r=site/deletepermissions",
														        	data: "permission_id="+$node.id,
														        	success: function(data_ajax,status){
														        		_deleteNode($node, 'jstree-v_f_shras');
														        	}
														        });
										                        
										                    }
										                }

										            };
									        	} else if($node.li_attr['data-perm-level'] == 2) {
									        		return {
										            	"Read": {
										                    "label": "Запретить чтение",
										                    "icon " : 'read-icon',
										                    "_disabled" : function (obj) { 
										                    	if($node.parent == '#') {
											                    	return true;
											                    } else {
											                    	return false;
											                    }

											                },
										                    "action": function (obj) {
																_setPermLevel($node.id, 0, 'jstree-v_f_shras');							                        
										                    }
										                },
										                "Write": {
										                    "label": "Запретить запись",
										                    "icon " : 'write-icon',
										                    "_disabled" : function (obj) { 
										                    	if($node.parent == '#') {
											                    	return true;
											                    } else {
											                    	return false;
											                    }

											                },
										                    "action": function (obj) {
																_setPermLevel($node.id, 1, 'jstree-v_f_shras');												                        
										                    }
										                },
										                "Delete": {
										                    "label": "Удалить",
										                    "icon " : 'delete-icon',
										                    "_disabled" : function (obj) { 
										                    	if($node.parent == '#') {
											                    	return true;
											                    } else {
											                    	return false;
											                    }

											                },
										                    "action": function (obj) {
										            			
										            			$.ajax({
														        	type: "POST",
														        	dataType: 'json',
														        	url: "index.php?r=site/deletepermissions",
														        	data: "permission_id="+$node.id,
														        	success: function(data_ajax,status){
														        		_deleteNode($node, 'jstree-v_f_shras');
														        	}
														        });
										                        
										                    }
										                }

										            };
									        	} 
									            
									        }
									    },
										"core": {
								            "check_callback": function(operation, node, node_parent, node_position, more) {
								                    var isset_children = 'true';
								                    for (var i = 0; i <= node_parent.children.length; i++) {
								                    	if($('#jstree-v_f_shras').jstree(true).get_node(node_parent.children[i]).text === node.text) {
								                    		isset_children = 'false';
								                    	} 
								                    }
								                    if(more && more.dnd && (operation === 'move_node' || operation === 'copy_node') && (node_parent.id === '#' || node_parent.parents.length != 1 || isset_children === 'false' )) {
													    return false;
													}
													return true;
								                },
								            "data" : [
									        	<?php
									        		foreach($v_f_shras as $v_f_shra) {
									        			echo '{"id" : "v_f_shra_'.$v_f_shra['id'].'", "icon" : "glyphicon glyphicon-certificate", "parent" : "#", "text" : "'.$v_f_shra['name'].'", "li_attr" : { "data-panel" : "v_f_shra", "data-id" : "'.$v_f_shra['id'].'" }},';
									        			$inner_list = \app\models\Permissions::find()->where(['SUBJECT_TYPE' => 1, 'SUBJECT_ID' => $v_f_shra['id'], 'DEL_TRACT_ID' => 0])->orderBy('PERM_TYPE')->all();
														if($inner_list) {
															foreach($inner_list as $li) {
																if($li->PERM_TYPE == 1) {
																	$result_li = \app\models\Actions::findOne($li->ACTION_ID);
																	echo '{"id" : "'.$li->ID.'", "icon" : "glyphicon glyphicon-cog", "parent" : "v_f_shra_'.$v_f_shra['id'].'", "text" : "'.$result_li->ACTION_DESC.'", "li_attr" : { "data-id" : "'.$li->ID.'", "data-perm-level" : "'.$li->PERM_LEVEL.'", "class" : "inner-node-state"}},';
																} elseif($li->PERM_TYPE == 2) {
																	$result_li = \app\models\States::findOne($li->ACTION_ID);
																	echo '{"id" : "'.$li->ID.'", "icon" : "glyphicon glyphicon-check", "parent" : "v_f_shra_'.$v_f_shra['id'].'", "text" : "'.$result_li->STATE_NAME.'", "li_attr" : { "data-id" : "'.$li->ID.'", "data-perm-level" : "'.$li->PERM_LEVEL.'", "class" : "inner-node-state"}},';
																}
															}
									        			}
									        		}
									        	?>
									        ],
								        },
								        "search": {
								            "case_insensitive": true,
								            "show_only_matches" : true
								        },
								        "plugins" : [
										    "dnd",
										    "actions",
										    "contextmenu",
										    "search"
										],
									})
									.on("copy_node.jstree", function (e, data) {
									  	//store in database
									  	//console.log($('#jstree-v_f_shras').jstree(true).get_node(data.node.parents[0]));

									  	var parent_id = $('#jstree-v_f_shras').jstree(true).get_node(data.node.parents[0]).li_attr['data-id'];
									  	var parent_type = $('#jstree-v_f_shras').jstree(true).get_node(data.node.parents[0]).li_attr['data-panel'];
									  	var original_id = data.original.li_attr['data-id'];
									  	var original_type = data.original.li_attr['data-panel'];

									  	$.ajax({
								        	type: "POST",
								        	dataType: 'json',
								        	url: "index.php?r=site/setpermissions",
								        	data: "parent_id="+parent_id+"&parent_type="+parent_type+"&original_id="+original_id+"&original_type="+original_type,
								        	success: function(data_ajax,status){
								        		$('#jstree-v_f_shras').jstree(true).set_id(data.node, data_ajax.inserted_id);
								        		$('#jstree-v_f_shras').jstree('get_node', data.node).li_attr['data-perm-level'] = 1;
								        		//$("#jstree-v_f_shras").jstree(true).refresh_node(data.node);

								        		$("#jstree-v_f_shras").jstree(true).get_node(data_ajax.inserted_id).li_attr.class = "inner-node-state";
												$("#jstree-v_f_shras").jstree(true).redraw_node(data_ajax.inserted_id, true);

								        		$('#'+data_ajax.inserted_id).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
								    			$('#'+data_ajax.inserted_id).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');

								    			$('#jstree-v_f_shras li.inner-node-state').each(function(){
										    		if ($(this).find("i.action_write").length == 0 && $(this).find("i.action_read").length == 0) {
										    			var perm_level = $(this).attr('data-perm-level');
										    			if(perm_level == 0) {
										    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
										    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-close" style="color: #ff0000;"></i> ');
										    			} else if(perm_level == 1) {
										    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
										    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
										    			} else if(perm_level == 2) {
										    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-save" style="color: #1AC94F;"></i> ');
										    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
										    			}
										    		}
										    		
												});

								        	}
								        });
									})
									.on("select_node.jstree", function(evt, data){
								        $('#jstree-v_f_pers').jstree("deselect_all");   
								        $('#jstree-actions').jstree("deselect_all");   
								        $('#jstree-states').jstree("deselect_all");        
								    }).on("open_node.jstree", function(e, data) {
								    	
								    	$('#jstree-v_f_shras li.inner-node-state').each(function(){
								    		if ($(this).find("i.action_write").length == 0 && $(this).find("i.action_read").length == 0) {
								    			var perm_level = $(this).attr('data-perm-level');
								    			if(perm_level == 0) {
								    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
								    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-close" style="color: #ff0000;"></i> ');
								    			} else if(perm_level == 1) {
								    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
								    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
								    			} else if(perm_level == 2) {
								    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-save" style="color: #1AC94F;"></i> ');
								    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
								    			}
								    		}
								    		
										});
								    });

								    
								</script>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="permissions-form-left-headingTwo">
								<h4 class="panel-title">
									<a role="button" data-toggle="collapse" data-parent="#permissions-form-left-headingTwo" href="#collapse-permissions-form-left-Two" aria-expanded="false" aria-controls="collapse-permissions-form-left-Two">
										Сотрудники
									</a>
								</h4>
							</div>
							<div id="collapse-permissions-form-left-Two" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="permissions-form-left-headingTwo">
								<div class="panel-body" style="padding: 10px 0;">

									<div style="padding: 0 0 5px 10px;">
										<form role="form" id="search_v_f_pers_form" name="search_v_f_pers_form">  
											<div class="row">
												<div class="col-xs-12">
													<div class="input-group">
														<input type="text" class="form-control" class="form-control" id="search-input-pers" placeholder="Введите текст для поиска.." />
														<div class="input-group-btn">
															<button type="submit" class="btn btn-primary">Найти</button>
														</div><!-- /btn-group -->
														<div class="input-group-btn">
															<button type="reset" class="btn btn-default" onclick="$('#jstree-v_f_pers').jstree(true).search('');">Сбросить</button>
														</div><!-- /btn-group -->
													</div><!-- /input-group -->
												</div><!-- /.col-xs-12 -->
											</div><!-- /.row -->
										</form>
									</div>

									<div id="jstree-v_f_pers"></div>
									<script type="text/javascript">
										function _deleteNodePers(node, tree) {
											var tree = $("#"+tree).jstree(true);
											tree.delete_node(node);
											$('#jstree-v_f_pers li.inner-node-state').each(function(){
									    		if ($(this).find("i.action_write").length == 0 && $(this).find("i.action_read").length == 0) {
									    			var perm_level = $(this).attr('data-perm-level');
									    			if(perm_level == 0) {
									    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
									    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-close" style="color: #ff0000;"></i> ');
									    			} else if(perm_level == 1) {
									    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
									    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
									    			} else if(perm_level == 2) {
									    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-save" style="color: #1AC94F;"></i> ');
									    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
									    			}
									    		}
									    		
											});
										}

										$("#search_v_f_pers_form").submit(function(e) {
										  	e.preventDefault();
										  	$("#jstree-v_f_pers").jstree(true).search($("#search-input-pers").val());
										});

										$("#jstree-v_f_pers").jstree({
											"search": {
									            "case_insensitive": true,
									            "show_only_matches" : true
									        },
											"plugins" : [
											    "dnd",
											    "contextmenu",
											    "search"
											],
											"dnd" : {
									            "is_draggable" : false,
									        }, 
									        "contextmenu": {
										        "items": function ($node) {
										        	var tree = $("#jstree-v_f_pers").jstree(true);
										            if($node.li_attr['data-perm-level'] == 0) {
										        		return {
											            	"Read": {
											                    "label": "Разрешить чтение",
											                    "icon " : 'read-icon',
											                    "_disabled" : function (obj) { 
											                    	if($node.parent == '#') {
												                    	return true;
												                    } else {
												                    	return false;
												                    }

												                },
											                    "action": function (obj) {
																	_setPermLevel($node.id, 1, 'jstree-v_f_pers');							                        
											                    }
											                },
											                "Write": {
											                    "label": "Разрешить запись",
											                    "icon " : 'write-icon',
											                    "_disabled" : function (obj) { 
											                    	if($node.parent == '#') {
												                    	return true;
												                    } else {
												                    	return false;
												                    }

												                },
											                    "action": function (obj) {
																	_setPermLevel($node.id, 2, 'jstree-v_f_pers');												                        
											                    }
											                },
											                "Delete": {
											                    "label": "Удалить",
											                    "icon " : 'delete-icon',
											                    "_disabled" : function (obj) { 
											                    	if($node.parent == '#') {
												                    	return true;
												                    } else {
												                    	return false;
												                    }

												                },
											                    "action": function (obj) {
											            			
											            			$.ajax({
															        	type: "POST",
															        	dataType: 'json',
															        	url: "index.php?r=site/deletepermissions",
															        	data: "permission_id="+$node.id,
															        	success: function(data_ajax,status){
															        		_deleteNodePers($node, 'jstree-v_f_pers');
															        	}
															        });
											                        
											                    }
											                }

											            };
										        	} else if($node.li_attr['data-perm-level'] == 1) {
										        		return {
											            	"Read": {
											                    "label": "Запретить чтение",
											                    "icon " : 'read-icon',
											                    "_disabled" : function (obj) { 
											                    	if($node.parent == '#') {
												                    	return true;
												                    } else {
												                    	return false;
												                    }

												                },
											                    "action": function (obj) {
																	_setPermLevel($node.id, 0, 'jstree-v_f_pers');				                        
											                    }
											                },
											                "Write": {
											                    "label": "Разрешить запись",
											                    "icon " : 'write-icon',
											                    "_disabled" : function (obj) { 
											                    	if($node.parent == '#') {
												                    	return true;
												                    } else {
												                    	return false;
												                    }

												                },
											                    "action": function (obj) {
																	_setPermLevel($node.id, 2, 'jstree-v_f_pers');											                        
											                    }
											                },
											                "Delete": {
											                    "label": "Удалить",
											                    "icon " : 'delete-icon',
											                    "_disabled" : function (obj) { 
											                    	if($node.parent == '#') {
												                    	return true;
												                    } else {
												                    	return false;
												                    }

												                },
											                    "action": function (obj) {
											            			
											            			$.ajax({
															        	type: "POST",
															        	dataType: 'json',
															        	url: "index.php?r=site/deletepermissions",
															        	data: "permission_id="+$node.id,
															        	success: function(data_ajax,status){
															        		_deleteNodePers($node, 'jstree-v_f_pers');
															        	}
															        });
											                        
											                    }
											                }

											            };
										        	} else if($node.li_attr['data-perm-level'] == 2) {
										        		return {
											            	"Read": {
											                    "label": "Запретить чтение",
											                    "icon " : 'read-icon',
											                    "_disabled" : function (obj) { 
											                    	if($node.parent == '#') {
												                    	return true;
												                    } else {
												                    	return false;
												                    }

												                },
											                    "action": function (obj) {
																	_setPermLevel($node.id, 0, 'jstree-v_f_pers');							                        
											                    }
											                },
											                "Write": {
											                    "label": "Запретить запись",
											                    "icon " : 'write-icon',
											                    "_disabled" : function (obj) { 
											                    	if($node.parent == '#') {
												                    	return true;
												                    } else {
												                    	return false;
												                    }

												                },
											                    "action": function (obj) {
																	_setPermLevel($node.id, 1, 'jstree-v_f_pers');												                        
											                    }
											                },
											                "Delete": {
											                    "label": "Удалить",
											                    "icon " : 'delete-icon',
											                    "_disabled" : function (obj) { 
											                    	if($node.parent == '#') {
												                    	return true;
												                    } else {
												                    	return false;
												                    }

												                },
											                    "action": function (obj) {
											            			
											            			$.ajax({
															        	type: "POST",
															        	dataType: 'json',
															        	url: "index.php?r=site/deletepermissions",
															        	data: "permission_id="+$node.id,
															        	success: function(data_ajax,status){
															        		_deleteNodePers($node, 'jstree-v_f_pers');
															        	}
															        });
											                        
											                    }
											                }

											            };
										        	} 
										        }
										    },
											"core": {
									            "check_callback": function(operation, node, node_parent, node_position, more) {
									                var isset_children = 'true';
								                    for (var i = 0; i <= node_parent.children.length; i++) {
								                    	if($('#jstree-v_f_pers').jstree(true).get_node(node_parent.children[i]).text === node.text) {
								                    		isset_children = 'false';
								                    	} 
								                    }
								                    if(more && more.dnd && (operation === 'move_node' || operation === 'copy_node') && (node_parent.id === '#' || node_parent.parents.length != 1 || isset_children === 'false' )) {
													    return false;
													}
													return true;
									            },
									            "data" : [
										        	<?php
										        		foreach($v_f_pers as $v_f_per) {
										        			echo '{"id" : "v_f_per_'.$v_f_per['tn'].'", "icon" : "glyphicon glyphicon-user", "parent" : "#", "text" : "'.$v_f_per['fio'].'", "li_attr" : { "data-panel" : "v_f_pers", "data-id" : "'.$v_f_per['tn'].'" }},';
										        			$inner_list = \app\models\Permissions::find()->where(['SUBJECT_TYPE' => 2, 'SUBJECT_ID' => $v_f_per['tn'], 'DEL_TRACT_ID' => 0])->orderBy('PERM_TYPE')->all();
															if($inner_list) {
																foreach($inner_list as $li) {
																	if($li->PERM_TYPE == 1) {
																		$result_li = \app\models\Actions::findOne($li->ACTION_ID);
																		echo '{"id" : "'.$li->ID.'", "icon" : "glyphicon glyphicon-cog", "parent" : "v_f_per_'.$v_f_per['tn'].'", "text" : "'.$result_li->ACTION_DESC.'", "li_attr" : { "data-id" : "'.$li->ID.'", "data-perm-level" : "'.$li->PERM_LEVEL.'", "class" : "inner-node-state"}},';
																	} elseif($li->PERM_TYPE == 2) {
																		$result_li = \app\models\States::findOne($li->ACTION_ID);
																		echo '{"id" : "'.$li->ID.'", "icon" : "glyphicon glyphicon-check", "parent" : "v_f_per_'.$v_f_per['tn'].'", "text" : "'.$result_li->STATE_NAME.'", "li_attr" : { "data-id" : "'.$li->ID.'", "data-perm-level" : "'.$li->PERM_LEVEL.'", "class" : "inner-node-state"}},';
																	}
																}
										        			}
										        		}
										        	?>
										        ],
									        },
										}).on("copy_node.jstree", function (e, data) {
										  	//store in database
										  	var parent_id = $('#jstree-v_f_pers').jstree(true).get_node(data.node.parents[0]).li_attr['data-id'];
										  	var parent_type = $('#jstree-v_f_pers').jstree(true).get_node(data.node.parents[0]).li_attr['data-panel'];
										  	var original_id = data.original.li_attr['data-id'];
									  		var original_type = data.original.li_attr['data-panel'];

										  	$.ajax({
									        	type: "POST",
									        	dataType: 'json',
									        	url: "index.php?r=site/setpermissions",
									        	data: "parent_id="+parent_id+"&parent_type="+parent_type+"&original_id="+original_id+"&original_type="+original_type,
									        	success: function(data_ajax,status){
									        		$('#jstree-v_f_pers').jstree(true).set_id(data.node, data_ajax.inserted_id);
									        		$('#jstree-v_f_pers').jstree('get_node', data.node).li_attr['data-perm-level'] = 1;

									        		$("#jstree-v_f_pers").jstree(true).get_node(data_ajax.inserted_id).li_attr.class = "inner-node-state";
													$("#jstree-v_f_pers").jstree(true).redraw_node(data_ajax.inserted_id, true);

									        		$('#'+data_ajax.inserted_id).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
									    			$('#'+data_ajax.inserted_id).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');

									    			$('#jstree-v_f_pers li.inner-node-state').each(function(){
											    		if ($(this).find("i.action_write").length == 0 && $(this).find("i.action_read").length == 0) {
											    			var perm_level = $(this).attr('data-perm-level');
											    			if(perm_level == 0) {
											    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
											    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-close" style="color: #ff0000;"></i> ');
											    			} else if(perm_level == 1) {
											    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
											    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
											    			} else if(perm_level == 2) {
											    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-save" style="color: #1AC94F;"></i> ');
											    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
											    			}
											    		}
											    		
													});

									        	}
									        });
										  	
										}).on("select_node.jstree", function(evt, data){
									        $('#jstree-v_f_shras').jstree("deselect_all"); 
									        $('#jstree-actions').jstree("deselect_all");   
									        $('#jstree-states').jstree("deselect_all");        
									    }).on("open_node.jstree", function(e, data) {
								    	
									    	$('#jstree-v_f_pers li.inner-node-state').each(function(){
									    		if ($(this).find("i.action_write").length == 0 && $(this).find("i.action_read").length == 0) {
									    			var perm_level = $(this).attr('data-perm-level');
									    			if(perm_level == 0) {
									    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
									    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-close" style="color: #ff0000;"></i> ');
									    			} else if(perm_level == 1) {
									    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-remove" style="color: #ff0000;"></i> ');
									    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
									    			} else if(perm_level == 2) {
									    				$(this).prepend(' <i class="action_write pull-right inner-node-state-action glyphicon glyphicon-floppy-save" style="color: #1AC94F;"></i> ');
									    				$(this).prepend(' <i class="action_read pull-right inner-node-state-action glyphicon glyphicon-eye-open" style="color: #1AC94F;"></i> ');
									    			}
									    		}
									    		
											});
									    });
									</script>
								</div>
							</div>
						</div>

					</div>

				</div>
				<div class="col-md-6">
					
					<div class="panel-group" id="accordion-permissions-form-right" role="tablist" aria-multiselectable="true">
						
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="permissions-form-right-headingOne" style="position: relative;">
								<h4 class="panel-title">
									<a role="button" data-toggle="collapse" data-parent="#permissions-form-right-headingOne" href="#collapse-permissions-form-right-One" aria-expanded="false" aria-controls="collapse-permissions-form-right-One">
										Действия
									</a>
								</h4>
								<div id="permissions-header-actions-filter"><span><i class="glyphicon glyphicon-search"></i> фильтр</span></div>
							</div>
							<div id="collapse-permissions-form-right-One" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="permissions-form-right-headingOne">
								<div class="panel-body">
									<div id="jstree-actions"></div>
									<script type="text/javascript">
										$("#jstree-actions").jstree({
											"plugins" : [
											    "dnd",
											],
											"dnd" : {
									            "always_copy" : true,
									        }, 
											"core": {
												"data" : [
													<?php 
														foreach($actions as $action) {
															echo '{"id" : "actions_'.$action->ID.'", "icon" : "glyphicon glyphicon-cog", "parent" : "#", "text" : "'.$action->ACTION_DESC.'", "li_attr" : { "data-panel" : "actions", "data-id" : "'.$action->ID.'" }},';
														} 
													?>
												]
											}
										});

									</script>
									
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="permissions-form-right-headingTwo" style="position: relative;">
								<h4 class="panel-title">
									<a role="button" data-toggle="collapse" data-parent="#permissions-form-right-headingTwo" href="#collapse-permissions-form-right-Two" aria-expanded="false" aria-controls="collapse-permissions-form-right-Two">
										Состояния
									</a>
								</h4>
								<div id="permissions-header-states-filter"><span><i class="glyphicon glyphicon-search"></i> фильтр</span></div>
							</div>
							<div id="collapse-permissions-form-right-Two" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="permissions-form-right-headingTwo">
								<div class="panel-body" id="jstree-states">
									<ul>
										<?php
											foreach($states_list as $state) {
										?>
											<li data-jstree='{"icon":"glyphicon glyphicon-check"}' data-panel="states" id="state<?= $state->ID; ?>" data-id="<?= $state->ID; ?>" style="padding-top: 5px; font-size: 11px;"><?= $state->STATE_NAME; ?></li>
										<?php 
											}
										?>
									</ul>
								</div>
							</div>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>