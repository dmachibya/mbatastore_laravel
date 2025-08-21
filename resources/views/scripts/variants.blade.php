<script type="text/javascript">
  ;
  (function($, window, document) {
    $(document).ready(function() {
      // Attach change event to category_list dropdown
      $('select[name="category_list[]"]').on('change', function() {
        // $('#attributesFieldset').html('');
        var selectedCategoryIds = $(this).val();

        // Ajax request to fetch corresponding attributes based on selected categories
        $.ajax({
          url: '{{ route('admin.stock.product.getAttributesByCategories') }}',
          type: 'GET',
          data: {
            category_ids: selectedCategoryIds
          },
          success: function(data) {
            // Update the attributes fieldset with the fetched attributes
            $('#attributesFieldset').html(data);

            // Initiate select2
            $(".select2-set_attribute").select2({
              placeholder: "{{ trans('app.choose_attributes') }}",
            });

            $('#set-variant-btn-block').removeClass('hide');
          },
          error: function(error) {
            console.error('Error fetching attributes:', error);
          }
        });
      });

      // Set Combinations
      $("#setCombinations").on("click", function(e) {
        e.preventDefault();

        var options = {};
        var all_ok = true;
        $(".select2-set_attribute").each(function(indx, attrs) {
          var attrID = $(this).attr('id');
          var attrName = $(this).attr('name');
          var attrValues = $(attrs).val();

          if (attrValues.length == 0) {
            all_ok = false;

            return false;
          }

          if (attrValues.length) {
            options[attrID] = attrValues;
          }
        });

        if (all_ok == false) {
          $("#set-variant-btn-block > p").addClass('text-danger lead');
          $("#set-variant-btn-block > button").addClass('btn-danger');
          // alert('sss');
          return false;
        } else {
          $("#set-variant-btn-block > p").removeClass('text-danger lead');
          $("#set-variant-btn-block > button").removeClass('btn-danger');
        }

        var url = "{{ route('admin.stock.product.getCombinations') }}";
        $.ajax({
          url: url,
          data: options,
          async: false,
          success: function(variants) {
            $('#myAttributes').collapse('hide');
            $('#combinationsPlaceholder').html(variants);
          }
        });

        var sku = $('input[name="sku"]').val();
        var price = $('input[name="sale_price"]').val();
        var purchasePrice = $('input[name="purchase_price"]').val();
        var quantity = $('input[name="stock_quantity"]').val();

        $("tr.variant-row").each(function(indx, row) {
          if (sku) {
            $(this).find('.variant-sku').val(sku + '-' + (indx + 1));
          }

          if (price) {
            $(this).find('.variant-price').val(price);
          }

          if (purchasePrice) {
            $(this).find('.variant-purchase-price').val(purchasePrice);
          }

          $(this).find('.variant-qtt').val(quantity);
        });
      });

      // Preview image on select
      $('body').on('change', '.variant-img', function() {
        var img = $(this).next("img");
        var reader = new FileReader();
        reader.onload = function(e) {
          img.attr("src", e.target.result); // get loaded data and render preview.
        };

        reader.readAsDataURL(this.files[0]); // read the image file as a data URL.
      });

      $('body').on('click', '.deleteThisRow', function() {
        $(this).closest('tr').remove();
      });

    });
  }(window.jQuery, window, document));
</script>
