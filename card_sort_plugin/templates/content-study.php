<?php 
/* Template for card sort study */
// global $post;
$study_meta = get_post_meta( $post->ID );
$anthrohack_settings = get_option( 'anthrohack_settings' ); 
?>
<div id="card_sort_study" class="">
	<div class="study-header">
		<?php /*<h1 class="title"><?php echo get_the_title(); ?></h1>*/ ?>

		<?php //study description
		if(anthrohack_check_meta_var($study_meta, "description")){ ?>
			<!-- begin accordion wrap -->
			<div class="accordion-wrapper collapsed">
		        
		        <div type="button" class="accordion-btn" data-toggle="collapse" data-target="#content_protocol">
		        	<h3 class="title">Research protocol<span class="expand">Click to expand <i class="icon-anthrohack-chevron-down"></i></span></h3>
				</div> 

				<!-- Collapsible Element HTML -->
	        	<div id="content_protocol" class="collapse">
					<?php echo do_shortcode($study_meta["description"][0]); ?>
				</div>
			</div>
		<?php } ?>

		
		<?php //render Questions
		if(anthrohack_check_meta_var($study_meta, "anthrohack_questions")){
			$questions = json_decode($study_meta["anthrohack_questions"][0], true); 
			if($questions){ ?>
				<div class="accordion-wrapper collapsed">
					<div type="button" class="accordion-btn" data-toggle="collapse" data-target="#questions">
			        	<h3 class="title">Questions<span class="expand">Click to expand <i class="icon-anthrohack-chevron-down"></i></span></h3>
					</div> 

					<!-- Collapsible Element HTML -->
		        	<div id="questions" class="collapse">
						<?php foreach ($questions as $question) { 
							$slug = anthrohack_check_meta_var($question, 'section_slug');
							$id_number = anthrohack_check_meta_var($question, 'section_id_number'); 

							if($slug && $id_number){ ?>

								<div class="question">

									<?php if(anthrohack_check_meta_var($question, $slug . '_hero_image')){ ?>
										<div class="question-image"><img src="<?php echo $question[$slug . '_hero_image']; ?>" alt="<?php echo $slug; ?>" /></div>
									<?php } ?>

									<div class="description">
									<?php if(anthrohack_check_meta_var($question, $slug . '_content')){ ?>
										<?php echo do_shortcode( base64_decode($question[$slug . '_content'])); ?>
									<?php }else{ ?>
										<?php echo $question['section_title']; ?>
									<?php } ?>
									</div>

									<textarea class="answer" data-id="<php echo $id_number; ?>" id="<php echo $slug; ?>"></textarea>
								</div><!--end question -->

						<?php } //end if slug + id
						} //end foreach ?>
					</div>
				</div>		
			<?php }//end if questions 
		} //end check_meta_var	?>
	</div>

	<div class="study-content">
		<div class="board">

			<?php //cards ?>
			<div class="board-column pile" id="unsorted" data-id="0">
				<div class="board-column-header">
					<h4 class="title">Unsorted</h4>
					<div class="description"><?php echo anthrohack_check_meta_var($study_meta, "cards_instructions", "Drag cards onto a pile."); ?></div>
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

									<div class="board-item card" id="<?php echo $slug; ?>" data-id="<?php echo $id; ?>">
										<div class="board-item-content card-content">

											<h5 class="title"><?php echo $card["section_title"]; ?></h5>

											<?php if(anthrohack_check_meta_var($card, $slug . "_bg_image")){ ?>
												<div class="bg_image full-bleed" style="background:url(<?php echo $card[$slug . "_bg_image"]; ?>);"></div>
											<?php } ?>
												

											<?php if(anthrohack_check_meta_var($card, $slug . "_content")){ ?>
												<div class="description"><?php echo $card[$slug . "_content"]; ?></div>
											<?php } ?>
										</div>
									</div>

								<? } //end if slug + id
							} //end foreach
						}
					}	
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
											<?php if(anthrohack_check_meta_var($pile, $slug . '_description')){ ?>
												<div class="description"><?php echo $pile[$slug . '_description']; ?></div>
											<?php } ?>	
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
				}else{
					//echo "No piles yet!";
				}

			}else{ // if no piles show "add pile" button ?>

				<div class="board-item add-pile" id="add_piles">
					<div class="board-item-content">
						<button class="add-more-items btn btn-primary"><i class="fa fa-icon-plus"></i>Add more items</button>
					</div>
				</div>
				
			<?php } //end piles  ?>
			
		</div> <?php //end board ?>

	</div>

	<div class="study-content-footer add-pile" ">
		<div class="content">
			<input class="submit" type="button" value="Submit" onClick="window.location.reload()">
			<!-- <button class="submit">Submit</button> -->
		</div>
	</div>

</div>
