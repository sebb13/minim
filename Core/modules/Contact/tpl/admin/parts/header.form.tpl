<div class="row form-group">
	<form action="#" method="POST" id="searchMsgForm">
		<div class="col-md-4">
			<button type="button" class="btn btn-default btn-md showMessagesButton">{__MESSAGES_RECEIVED__}</button>
			<button type="button" class="btn btn-default btn-md showArchivedMessagesButton">{__ARCHIVED_MESSAGES__}</button>
		</div>
	</form>
	<div class="col-md-8 search-form">
		<form action="#" method="POST" id="searchMsgForm">
			<div class="row form-group">
				<div class="col-md-10">
					<input type="text" name="msgKeyword" id="msgKeyword" value="{__KEYWORD__}" class="form-control" />
				</div>
				<div class="col-md-2 text-right">
					<input type="button" id="msgSearchButton" value="{__SEARCH__}" class="btn btn-default btn-md" />
				</div>
			</div>
		</form>
	</div>
</div>