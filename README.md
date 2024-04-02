# LM Bought Together

This module allows the website administrator to add an additional list of products on the product page. It retrieves from the database the products that have been most frequently purchased together.

## Installation

You will need to add an extra hook in the `product.tpl` file at the desired location:

`{hook h='displayProductBoughtTogether' id_product=$product.id_product}`

## Configuration

Navigate to the configuration page and generate a new token to populate the newly created table.

## Usage

To populate the table, set up a new cron job using the following URL:

`/module/lm_boughttogether/generate?key=your-token`