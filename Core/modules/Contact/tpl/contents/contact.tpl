<!-- container -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="home.html">Accueil</a></li>
		<li class="active">Contact</li>
	</ol>
	<div class="row">
		<!-- Article main content -->
		<article class="col-sm-12 maincontent">
			<header class="page-header">
				<h1 class="page-title">{__CONTACT__} <i class="fa fa-envelope-o"></i></h1>
			</header>
			
			<div class="row div-contact-form">
				<div class="col-md-12 contact-container">
					<h3 class="text-center">{__CONTACT_FRONT_TITLE__}</h3>
					<form action="#" method="POST" id="contactForm" enctype="multipart/form-data" accept-charset="UTF-8">
						<div class="row contact-row form-group">
							<div class="col-md-6">
								<label for="contact_name">{__CONTACT_NAME_LABEL__} <span class="mandatory-field">*</span></label>
								<input type="text" id="contact_name" name="contact_name" value="{__NAME__}" class="form-control">
							</div>
							<div class="col-md-6">
								<label for="contact_email">{__CONTACT_EMAIL_LABEL__} <span class="mandatory-field">*</span></label>
								<input type="text" id="contact_email" name="contact_email" value="{__EMAIL__}" class="form-control">
							</div>
						</div>
						<div class="row contact-row form-group">
							<div class="col-md-6">
								<label for="contact_subject">{__CONTACT_SUBJECT_LABEL__}</label>
								<input type="text" id="contact_subject" name="contact_subject" value="{__SUBJECT__}" class="form-control">
							</div>
							<div class="col-md-6">
								<label for="contact_file">{__CONTACT_FILE_LABEL__}</label>
								<input type="file" id="contact_file" name="contact_file" class="form-control">
							</div>
						</div>
						<div class="row contact-row form-group">
							<div class="col-md-12">
								<label for="contact_msg">{__CONTACT_MSG_LABEL__} <span class="mandatory-field">*</span></label>
								<textarea id="contact_msg" name="contact_msg" class="form-control">{__MSG__}</textarea>
								<div class="progress" style="display:none">
									<div id="progressbar" class="progress-bar-success progress-bar-striped active text-center" style="width:0">
										0%
									</div>
								</div>
							</div>
						</div>
						<div class="row tilala-fields">
							<div class="col-md-12">
								<fieldset>
									<legend>{__TILALA_FIELDS__}</legend>
									<input type="text" id="contact_email_2" name="contact_email_2" value="">
								</fieldset>
							</div>
							<div class="col-md-2"></div>
						</div>
						<div class="row contact-row">
							<div class="col-md-8 col-sm-6 mandatory-fields">
								{__MANDATORY_FIELD__}
							</div>
							<div class="col-md-4 col-sm-6 text-right">
								<input type="button" value="{__CONTACT_SEND_LABEL__}" class="contact-send-button btn-primary btn-sm">
							</div>
						</div>
						<input type="hidden" id="error_missing_name" value="{__ERROR_MISSING_NAME__}" />
						<input type="hidden" id="error_missing_email" value="{__ERROR_MISSING_EMAIL__}" />
						<input type="hidden" id="error_invalid_email" value="{__ERROR_INVALID_EMAIL__}" />
						<input type="hidden" id="error_missing_msg" value="{__ERROR_MISSING_MSG__}" />
					</form>
				</div>
			</div>
			</article>
		<!-- /Article -->
	</div>
</div>
<!-- /container -->