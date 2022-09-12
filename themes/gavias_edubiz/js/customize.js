 (function ($) {
  $(document).ready(function () {
  
    // === ColorPicker ===
    if($.fn.ColorPicker){
      $('input.color-picker').each(function(){
        var $input = $(this);
        var name = $input.attr('name');
        $( "<span class=\"color-check color-"+name+"\"></span>" ).insertAfter( $input );
        $input.ColorPicker({
          onChange:function (hsb, hex, rgb) {
           $('span.color-' + name).css('backgroundColor', '#' + hex);
            $input.val( '#' + hex );
          } 
       });
      });
    };

    $('input.color-picker').on('change', function(){
      var name = $(this).attr('name');
      $('span.color-' + name).css('backgroundColor', $(this).val());
    });

    $('input.color-picker').each(function(){
      $color = $(this).val();
      $name = $(this).attr('name');
      $('span.color-' + $name).css('backgroundColor', $color);
    });

  });

  $(window).load(function(){

    $('input.color-picker').each(function(){
      $color = $(this).val();
      $name = $(this).attr('name');
      $('span.color-' + $name).css('backgroundColor', $color);
    });
  })

  $(document).ready(function(){
    function getByClass(sClass){
      var aResult=[];
      var aEle=document.getElementsByTagName('*');
      for(var i=0;i<aEle.length;i++){
        /*foreach className*/
        var arr=aEle[i].className.split(/\s+/);
        for(var j=0;j<arr.length;j++){
          /*check class*/
          if(arr[j]==sClass){
            aResult.push(aEle[i]);
          }
        }
      }
      return aResult;
    };


    function runRender(type){
      var aBox = getByClass("code_"+type);
      for(var i=0;i < aBox.length; i++){
        var editor = false;
        if(!editor){
          editor = CodeMirror.fromTextArea(aBox[i], {
            lineNumbers: true,
            mode: "text/css",
          });
        }
        editor.on("blur", function() {editor.save()});
      }
    };

    runRender('css');
     $('a[href="#edit-css-customize"]').click(function(){
      runRender('css');
    })
  })

})(jQuery);

