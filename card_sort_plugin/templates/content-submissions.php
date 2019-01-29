<?php

$studies = anthrohack_get_studies(); 
$sorts = anthrohack_get_sorts();

?>

<div id="submissions">

	<div class="submissions-header">
		<h2 class="title">Study Submissions</h2>

		<p class="instructions">
			Select the submissions below and click export to downlaod a CSV.
		</p>

		<div class="submission-header-item">
			<label for="select_all_sorts"><input type="checkbox" id="select_all_sorts"> Select All</label>
		</div>

		<div class="submission-header-item">
			<label for="studies_picker">Filter by study</label>
			<select id="studies_picker">
				<option value="all" >All studies</option>
				<?php foreach ($studies as $study) {
					echo '<option value="'.$study->ID.'" >'.$study->post_title.'</option>';
				} ?>
			</select>
		</div>

		<div class="submission-header-item">
			<button type="button" class="download button"><span class="dashicons dashicons-download"></span>Export selected</button>
		</div>

		<div class="submission-header-item">
			<button type="button" class="delete button"><span class="dashicons dashicons-trash"></span>Delete selected</button>
		</div>
	</div>

	<?php foreach ($sorts as $sort) { 
		$sort_meta = get_post_meta( $sort->ID );
		$study = get_post($sort_meta['study_id'][0]);
		$study_edit_link = get_site_url() . '/wp-admin/post.php?post='.$sort_meta['study_id'][0].'&action=edit';
		$study_cards = [];
		if(anthrohack_check_meta_var($sort_meta, 'piles')){
			$piles = json_decode($sort_meta['piles'][0]);
		}else{
			$piles = false;
		}

		if($piles){
		//var_dump($piles); ?>

		<div class="sort" data-id="<?php echo $sort->ID; ?>" data-study_id="<?php echo $sort_meta['study_id'][0]; ?>">		
			
			<h3 class="title sort-title"><?php echo $sort->post_title; ?></h3>
			<div class="submission-meta">

				<div class="submission-select">
					<label for="select_sort_<?php echo $sort->ID; ?>"><input type="checkbox" class="select-sort" id="select_sort_<?php echo $sort->ID; ?>"> Select</label>
				</div>

				<div class="date"><label>Submission date: </label><?php echo get_the_date( 'D M j' , $sort->ID) . " at " . get_the_time("", $sort->ID); ?></div>
				<div class="study"><label>Study: </label><a href="<?php echo $study_edit_link; ?>" target="_blank"><?php echo $study->post_title; ?></a></div>
			</div>

			<?php if(anthrohack_check_meta_var($sort_meta, 'questions')){
					$questions = json_decode($sort_meta['questions'][0]);
			}else{
				$questions = false;
			} 

			if($questions){ ?>

				<div class="submission-questions">
					<h4 class="title">Questions</h4>
					<table id="question-table" >
						<tr>
							<th>Question</th><th>Answer</th>
						</tr>
						<?php foreach ($questions as $question) { ?>
							<tr>
								<td><?php echo anthrohack_check_meta_var($question, 'question_text'); ?></td>
								<td><?php echo anthrohack_check_meta_var($question, 'answer'); ?></td>
							</tr>
						<?php } ?>
					</table>
				</div>

			<?php } //end questions?>
			
			<div class="submission-piles">
				<h4 class="title">Piles</h4>
				<table id="pile-table" >
					<tr>
						<th>Pile ID#</th><th>Name</th><th>description</th><th>Card ID#s</th>
					</tr>
					<?php foreach ($piles as $pile) { 
						$card_ids = "";
						$first = true;
						foreach ($pile->cards as $card) {
							if(!$first)
								$card_ids .= ", ";
							$card_ids .= $card->id;

							//add card to study array
							$study_cards[] = array(
								'card_id' => $card->id,
								'card_title' => $card->card_title,
								'card_pile' => $pile->id,
							);

							$first = false;
						}
						?>
						<tr>
							<td><?php echo $pile->id ?></td>
							<td><?php echo $pile->pile_title ?></td>
							<td><?php echo $pile->sorter_notes ?></td>
							<td><?php echo $card_ids; ?></td>
						</tr>
					<?php } ?>
				</table>
			</div>

			<div class="submission-cards">
				<h4 class="title">Cards</h4>
				<table id="card-table" >
					<tr>
						<th>ID #</th><th>Name</th><th>Pile ID#</th>
					</tr>
					<?php 
					asort($study_cards);
					foreach ($study_cards as $card) { ?>
						<tr>
							<td><?php echo $card['card_id'] ?></td>
							<td><?php echo $card['card_title'] ?></td>
							<td><?php echo $card['card_pile'] ?></td>
						</tr>
					<?php } ?>
				</table>
			</div>

		</div>


	<?php }else{ ?>
			<br><br>No piles<br><br>
	<?php } //end questions
}//end sorts?>

</div>