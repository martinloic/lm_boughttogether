# Prestashop - LM Product Bought Together

The module get all previous orders that contain more than  one product in your store's database, and display to your customers the most purchased products along with the current product viewing.

When a customer visits a product page, they will be offered recommendations of the products that are most often bought together with the current product up to 4 products displayed.

## Installation

You will need to add an extra hook in the `product.tpl` file at the desired location:

`{hook h='displayProductBoughtTogether' id_product=$product.id_product}`

## Configuration

Navigate to the configuration page and generate a new token to populate the newly created table.

## Usage

To populate the table, set up a new cron job using the following URL:

`/module/lm_boughttogether/generate?key=your-token`
