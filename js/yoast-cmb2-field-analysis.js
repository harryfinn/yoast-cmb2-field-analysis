(function($) {
  $(function() {
    YoastCMB2FieldAnalysis = function() {
      YoastSEO.app.registerPlugin('YoastCMB2FieldAnalysis', {status: 'ready'});
      YoastSEO.app.registerModification(
        'content',
        this.addCMB2FieldsToContent,
        'YoastCMB2FieldAnalysis'
      );

      $('#post-body').find(
        'input[type=text][name*=_cmb2_], textarea[name*=_cmb2_]'
      ).on('keyup paste cut', function() {
        YoastSEO.app.pluginReloaded('YoastCMB2FieldAnalysis');
      });
    };

    YoastCMB2FieldAnalysis.prototype.addCMB2FieldsToContent = function(data) {
      var cmb2_content;

      $('#post-body').find(
        'input[type=text][name*=_cmb2_], textarea[name*=_cmb2_]'
      ).each(function() { cmb2_content += ' ' + $(this).val(); });

      return data + cmb2_content;
    };

    new YoastCMB2FieldAnalysis();
  });
})(jQuery);
