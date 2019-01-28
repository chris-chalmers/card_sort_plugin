<?php 
/* Template for card sort study */
// global $post;
$study_meta = get_post_meta( $post->ID );
$anthrohack_settings = get_option( 'anthrohack_settings' ); 
?>

<!-- Sort Submit Modal -->
<div class="modal anthrohack-modal fade" id="study_modal" role="dialog" aria-labelledby="study_modal" style="display: none;">
  <div class="modal-spacer"></div>
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
        	<h2 class="title"><?php echo anthrohack_check_meta_var($study_meta, "modal_title", "Almost there"); ?></h2>
        	<h5 class="subtitle"><?php echo anthrohack_check_meta_var($study_meta, "modal_subtitle", "Just a few more questions"); ?></h5>
        	<span class="close-button"><i class="icon-anthrohack-x"></i><br><span class="close-label">Close</span></span>
        </div>
		<div class="modal-body">

			<?php //render Questions
			if(anthrohack_check_meta_var($study_meta, "anthrohack_questions")){
				$questions = json_decode($study_meta["anthrohack_questions"][0], true); 
				if($questions){ ?>

			        	<div class="modal-questions">
			        		<h3 class="title">Survey questions</h3>	
							<?php foreach ($questions as $question) { 
								$slug = anthrohack_check_meta_var($question, 'section_slug');
								$id_number = anthrohack_check_meta_var($question, 'section_id_number'); 

								if($slug && $id_number){ ?>

									<div class="question" data-id="<?php echo $id_number; ?>" id="<?php echo $slug; ?>">

										<div class="description">
											<span>
												<?php if(anthrohack_check_meta_var($question, $slug . '_content')){ ?>
													<?php echo do_shortcode( base64_decode($question[$slug . '_content'])); ?>
												<?php }else{ ?>
													<?php echo $question['section_title']; ?>
												<?php } ?>
											</span>
											<?php if(anthrohack_check_meta_var($question, $slug . '_required')){ 
												$required = true; ?>
												<sup class="required">*</sup>
											<?php }else{
												$required = false;
											} ?>
										</div>

										<?php // render question field ?>
										<textarea class="answer <?php echo ($required != true)? 'required' : ''; ?>"></textarea>

									</div><!--end question -->

							<?php } //end if slug + id
							} //end foreach ?>
							<div class="required-note"><sup class="required">*</sup>Required question</div>
						</div>							
				<?php }//end if 
			} //end questions	?>

			<div class="modal-piles">

				<h3 class="title">Piles</h3>
				<div class="pile-instructions">
					<?php echo do_shortcode(anthrohack_check_meta_var($study_meta, "modal_description", "")); ?>
				</div>

				<?php //piles 
					//render piles if any ( Constrained )
					if(anthrohack_check_meta_var($study_meta, "anthrohack_piles")){
						$piles = json_decode($study_meta["anthrohack_piles"][0], true);
						// var_dump($piles);
						if($piles){
							if(is_array($piles)){
								foreach ($piles as $pile) { 
									$slug = anthrohack_check_meta_var($pile, 'section_slug');
									$id_number = anthrohack_check_meta_var($pile, 'section_id_number');

									if($slug && $id_number){ ?>
										<div class="pile" data-id="<?php echo $id_number; ?>" id="<?php echo $slug; ?>">
											<div class="pile-header">
												<h4 class="title"><?php echo $pile['section_title']; ?></h4>
											</div>
											<div class="pile-body">
												Cards: <ul class="pile-cards"></ul>
												<div class="clearfix"></div>
												<label>Notes:<sup class="required">*</sup></label>
												<textarea class="sorter_notes required"></textarea>
												<div class="required-note"><sup class="required">*</sup>Required</div>
											</div>
										</div>
									<? } //end if slug + id
								} //end foreach
							} //end if
						}
					} //end if piles ?>

					<?php //render modal pile template ?>
					<div class="pile sorter-defined" data-id="none" id="modal_pile_template">
						<div class="pile-header">
							<h4 class="title"><span></span><i class="edit-title icon-anthrohack-pencil"></i></h4>
						</div>
						<div class="pile-body">
							Cards: <ul class="pile-cards"></ul>
							<div class="clearfix"></div>
							<label>Notes:<sup class="required">*</sup></label>
							<textarea class="sorter_notes required"></textarea>
							<div class="required-note"><sup class="required">*</sup>Required</div>
						</div>
					</div>


			</div>
        </div>

        <div class="modal-footer" ">
        	<span class="cancel">Cancel</span>
			<input class="submit study-submit"  type="button" value="Submit">
			<div class="validation-message">Please fill in all of the required fields before submitting.</div>

		</div>

    </div>
  </div>
</div><!-- end Study modal -->

<div id="card_sort_study" class="" data-study_id="<?php echo $post->ID; ?>" data-study_slug="<?php echo $post->slug; ?>">
	<div class="study-header">
		<?php //study description
		if(anthrohack_check_meta_var($study_meta, "description")){ ?>
        	<h3 class="title">Research protocol</h3>
			<?php echo do_shortcode($study_meta["description"][0]); 
		} ?>
	</div>

	<div class="card-instructions">
		<h4 class="title">Instructions</h4>
		<?php echo anthrohack_check_meta_var($study_meta, "cards_instructions", "Drag cards onto a pile."); ?>
	</div>

	<div class="study-content">
		<div class="board">

			<?php //cards ?>
			<div class="board-column cards" id="unsorted" data-id="0">
				<div class="board-column-header">
					<h4 class="title">Unsorted</h4>
				</div>
				<div class="board-column-content">
				<?php //cards
				if(anthrohack_check_meta_var($study_meta, "anthrohack_cards")){
					//render cards
					$cards = json_decode($study_meta["anthrohack_cards"][0], true);
					if($cards){
						if(is_array($cards)){
							foreach ($cards as $card) { 
								$slug = anthrohack_check_meta_var($card, 'section_slug');
								$id_number = anthrohack_check_meta_var($card, 'section_id_number');

								if($slug && $id_number){ ?>

									<div class="board-item card" id="<?php echo $slug; ?>" data-id="<?php echo $id_number; ?>">
										<div class="board-item-content card-content">

											<h5 class="title"><?php echo $card["section_title"]; ?></h5>

											<?php if(anthrohack_check_meta_var($card, $slug . "_bg_image")){ ?>
												<div class="bg_image full-bleed" style="background:url(<?php echo $card[$slug . "_bg_image"]; ?>);"></div>
											<?php } ?>
												
										</div>
									</div>

								<? } //end if slug + id
							} //end foreach
						}
					} //end if cards	
				} ?>
				</div>
			</div> <?php //end cards ?>

			<?php //piles ?>
			<?php if(anthrohack_check_meta_var($study_meta, "constrained") == "yes" || anthrohack_check_meta_var($study_meta, "constrained") == "on" ){ 
				//render Constrained piles
				if(anthrohack_check_meta_var($study_meta, "anthrohack_piles")){

					// var_dump($study_meta["anthrohack_piles"][0]);

					$piles = json_decode($study_meta["anthrohack_piles"][0], true);
					// var_dump($piles);
					if($piles){
						if(is_array($piles)){
							foreach ($piles as $pile) { 
								$slug = anthrohack_check_meta_var($pile, 'section_slug');
								$id_number = anthrohack_check_meta_var($pile, 'section_id_number');

								if($slug && $id_number){ ?>
									<div class="board-column pile" id="<?php echo $slug; ?>" data-id="<?php echo $id_number; ?>">
										<div class="board-column-header">
											<h4 class="title"><?php echo $pile['section_title']; ?></h4>	
										</div>
										<div class="board-column-content">
																	
											<div class="placeholder">
												<div class="placeholder-content">Drag cards here</div>
											</div>		

										</div>
									</div>
								<? } //end if slug + id
							} //end foreach
						} //end if
					}

					$show_add_more = false;

				}else{
					//echo "No piles yet!";
					$show_add_more = true;
				}

				$show_add_more = false;

			}else{ // if no piles show "add pile" button 

				$show_add_more = true;

			} //end if constrained  

			if( $show_add_more ){ ?>

				<div class="board-column pile sorter-defined" id="pile_template" data-id="none">
					<div class="board-column-header">
						<h4 class="title"><span>Pile template</span><i class="remove-pile icon-anthrohack-cross"></i></h4>
					</div>
					<div class="board-column-content">
						<div class="placeholder">
							<div class="placeholder-content">Drag cards here</div>
						</div>		

					</div>
				</div>

				<div class="board-column add-pile" id="add_piles">
					<div class="board-column-content">
						<button class="add-more-items btn btn-primary"><i class="icon-anthrohack-plus"></i></i>Add a pile</button>
					</div>
				</div>
				
			<?php } //end piles  ?>
			
		</div> <?php //end board ?>

	</div>

	<div class="study-content-footer" >
		<div class="content">
			<span class="validation-message">
				<span>Please put all of the cards in piles before continuing.</span>
				<?php if(anthrohack_check_meta_var($study_meta, "constrained") != "yes" && anthrohack_check_meta_var($study_meta, "constrained") != "on" ){ ?>
					<span> There must be at least two piles and </span>
				<?php } ?>
				<span>Each pile must have at least <?php echo anthrohack_check_meta_var($study_meta, "min_cards"); ?> cards.</span>
			</span>
			<input class="submit study-finished" type="button" value="Next">
		</div>
	</div>

</div>
