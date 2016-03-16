<?php
	$this->title = 'Настройка следования состояний';
?>
<!-- Page Content -->
	<div id="page-content-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
						
						<div class="panel-group" id="accordion-states" role="tablist" aria-multiselectable="true">
							<?php
								
								if($states_list) {
									foreach($states_list as $state) {
							?>
							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="state-list-item-<?= $state->ID ?>">
									<h4 class="panel-title">
										<a class="collapsed" role="button" data-toggle="collapse" href="#collapse-state-item-<?= $state->ID ?>" aria-expanded="false" aria-controls="collapse-state-item-<?= $state->ID ?>">
											<?= $state->getState_name_state_colour_without_text().' '.$state->STATE_NAME; ?>
										</a>
									</h4>
								</div>
								<div id="collapse-state-item-<?= $state->ID ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="state-list-item-<?= $state->ID ?>">
									<div class="panel-body">
										<?php
											$inner_states = \app\models\States::find()->where(['!=','ID', $state->ID])->all();
											foreach($inner_states as $inner_state) {
												$state_next = \app\models\StatesNext::find()->where(['STATE_ID' => $state->ID, 'NEXT_STATE_ID'=>$inner_state->ID, 'DEL_TRACT_ID' => 0])->one();
												//if($state_next) echo $state_next->NEXT_STATE_ID;
										?>
											<div class="checkbox" style="font-size: 11px;"><label><input <?php if($perm_level != 2) { ?>disabled<?php } ?> type="checkbox" <?php if($state_next) { ?>checked<?php } ?> class="states-change-checkbox" data-parent="<?= $state->ID; ?>" name="States[<?= $state->ID; ?>][]" value="<?= $inner_state->ID; ?>"> <?= $inner_state->STATE_NAME ?></label></div>
										<?php
											}
										?>
									</div>
								</div>
							</div>
							<?php 
									}
								}
							?>
						</div>


					</div>
			</div>
		</div>
	</div>