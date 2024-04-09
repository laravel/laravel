<script>



    $.ajax({
        type: "GET",
        url: "/get-roles-view-status",
        
        dataType: "json",
        success: function(response) {

            if(response.status == 1 )
            {
                var data = response.data;

              var category =  data.find( record => record.rights_name == "category");
              var companies =  data.find( record => record.rights_name == "companies");
              var products = data.find( record => record.rights_name == "products");
              var user = data.find( record => record.rights_name == "user");

              if(category.is_view == 0){
                $("#category_menu_item_sidebar").remove();
              }
              if(companies.is_view == 0){
                $("#companies_menu_item_sidebar").remove();
              }
              if(products.is_view == 0){
                $("#products_menu_item_sidebar").remove();
              }
              if(user.is_view == 0){
                $("#user_menu_item_sidebar").remove();
              }

            }

        },
        error: function(error) {

        }
    });



    $(document).ready(function() {







    });
</script>