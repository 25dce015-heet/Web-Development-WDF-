fetch("products.json")
  .then(response => response.json())
  .then(products => {
    console.log("Total products:", products.length);
    products.forEach((product, index) => {
      console.log(`Entry ${index + 1}: ${product.prodname}`);
    });
  });


  