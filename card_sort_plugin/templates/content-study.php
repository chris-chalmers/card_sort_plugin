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
		        	<h3 class="title">Research protocol<span class="expand">Expand <i class="icon-anthrohack-chevron-down"></i></span></h3>
				</div> 

				<!-- Collapsible Element HTML -->
	        	<div id="content_protocol" class="collapse">
					<?php echo do_shortcode($study_meta["description"][0]); ?>
				</div>
			</div>
		<?php } ?>

		<div class="questions">
			<?php if(anthrohack_check_meta_var($study_meta, "anthrohack_questions")){
				//render Questions
				if(anthrohack_check_meta_var($study_meta, "anthrohack_questions")){
					$questions = json_decode($study_meta["anthrohack_questions"][0]);
					if($questions){
						foreach ($questions as $question) {
							# code...
						}
					}
				}
				
			} ?>
		</div>	

	</div>

	<div class="study-content">

			<div class="board">
				
				<div class="board-column cards" id="study_cards">
					<div class="board-column-header">
						<h4 class="title">Cards</h4>
						<div class="description"><?php echo anthrohack_check_meta_var($study_meta, "cards_instructions", "Drag cards onto a pile."); ?></div>
					</div>
					<div class="board-column-content">
					<?php //cards
					if(anthrohack_check_meta_var($study_meta, "anthrohack_cards")){
						//render cards
						$cards = json_decode($study_meta["anthrohack_cards"][0]);
						if($cards){
							if(is_array($cards)){
								foreach ($cards as $card) { ?>
									<div class="board-item card" id="<?php echo $card->slug; ?>" data-id="<?php echo $card->id; ?>">
										<div class="board-item-content card-content">
											<?php if(anthrohack_check_meta_var($card, "bg_image")){ ?>
												<div class="bg_image full-bleed" style="background:url(<?php echo $card["bg_image"]; ?>);"></div>
											<?php } ?>

											<?php if(anthrohack_check_meta_var($card, "title")){ ?>
												<h5 class="title"><?php echo $card["title"]; ?></h5>
											<?php } ?>

											<?php if(anthrohack_check_meta_var($card, "description")){ ?>
												<div class="description"><?php echo $card["description"]; ?></div>
											<?php } ?>
										</div>
									</div>
								<? }
							}
						}	
					} ?>
					</div>
				</div> <?php //end cards ?>

				<?php //piles
				if(anthrohack_check_meta_var($study_meta, "constrained") == "yes" || anthrohack_check_meta_var($study_meta, "constrained") == "on" ){ 
					//render Constrained piles
					if(anthrohack_check_meta_var($study_meta, "piles")){
						
						$piles = json_decode($study_meta["piles"][0]);
						if($piles){
							if(is_array($piles)){
								foreach ($piles as $pile) { ?>
									<div class="board-column pile" id="<?php echo $pile->slug; ?>" data-id="<?php echo $pile->id; ?>">
										<div class="board-column-header">
											<h4 class="title"><?php echo $pile->title; ?></h4>
											<div class="description"><?php echo $pile->description; ?></div>
										</div>
										<div class="board-column-content"></div>
									</div>
								<? } //end for
							} //end if
						}
					}else{
						echo "No piles yet!";
					}

				}else{ // show "add pile" button ?>
					<div class="board-column pile" id="<?php echo $pile->slug; ?>" data-id="<?php echo $pile->id; ?>">
						<div class="board-column-content">
							<button class="add-more-items btn btn-primary"><i class="material-icons"></i>Add more items</button>
						</div>
					</div>
				} ?>
				
			</div> <?php //end board ?>
		</div>
	</div>
</div>