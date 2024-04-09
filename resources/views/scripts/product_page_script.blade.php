<script>
    $(document).ready(function() {
        $.ajax({
            type: "GET",
            url: "/products-get-products-data",
            dataType: "json",
            success: function(response) {
                $.each(response.products, function(category_id, products) {
                    const productList = $('#product_list_' + category_id);

                    // Loop through each product in the category
                    $.each(products, function(index, product) {
                        // Create product card element
                        const productCard = $('<div>').addClass('bg-white rounded-lg overflow-hidden shadow-lg  mb-5').css('width', '17rem');

                        productCard.html(`
                            <a href="products-view/${product.id}">
                                <img src="storage/product_images/${product.image}" class="products_page_img" alt="${product.name}">
                                <div class="p-4">
                                    <h5 class="text-base font-semibold mb-1">${product.name}</h5>
                                    <p class="text-gray-700 text-base">${product.description}</p>
                                    <p class="text-gray-700 text-base">Price: ${product.price}</p>
                                    <div class="text-gray-700 text-base flex items-center">
                                    <span class="mr-2">Discount:</span>
                                    <span class="text-white bg-red-500 text-base px-2 py-1 rounded">${product.discount_percent}</span>
                                    </div>



                                    <a   id="${product.id  }" quantity="1" class=" add_to_cart block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 mt-4 rounded-lg text-center">Add to cart</a>
                                </div>
                            </a>
                        `);

                        // Append product card to product list container
                        productList.append(productCard);
                    });
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching products:', error);
            }
        });
    });
</script>