(function() {
	$(".btn-tinymce").click(function() {
		$this = $(this);
		$textarea = $this.parent().parent().find("textarea");
		if ($textarea.tinymce()) {
			$textarea.tinymce().destroy();
			$this.html("<i class=\"fa fa-bars fa-fw\"></i>");
		} else {
			$textarea.tinymce({
				plugins: "link image hr table code",
				element_format: "html",
				//content_css: "//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.min.css",
				browser_spellcheck : true,
				convert_urls: false,
				protect: [
					/<\?[^]*?\?>/g
				],
				setup: function(editor) {
					editor.on('PreInit', function(e) {
						//editor.iframeElement.contentDocument.querySelector("head link").remove();
						editor.iframeElement.contentDocument.getElementsByTagName('head')[0].innerHTML += "<style>"
						+ "body { "
						+ "font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif; "
						+ "font-size: 14px; "
						+ "line-height: 1.42857143; "
						+ "color: #333; "
						+ "}</style>";
					});
				}
			});
			$this.html("<i class=\"fa fa-code fa-fw\"></i>");
		}
	});
})();
