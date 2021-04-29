# STEP 1

add following snippet to product template
makr sure id is id="variation_amazon_asin_custom_link"

```
<a id="variation_amazon_asin_custom_link" href="https://www.amazon.in/stores/VISIARO/page/FCD29134-CC6B-45A3-91C7-4CEC7329400D?ref_=ast_bln" target="_blank" rel="noopener noreferrer">
    <img class="alignnone wp-image-94" src="http://localhost:8000/wp-content/uploads/WhatsApp-Image-2021-04-22-at-11.28.11-PM.jpeg" alt="" width="104" height="50" />
</a>
```

# STEP 2

Replace http://localhost:8000/wp-content/uploads/WhatsApp-Image-2021-04-22-at-11.28.11-PM.jpeg in above code with your amazon image location

# STEP 3

in each product,
in each product Variation,
add Amazon ASIN

# Settings
<img width="697" alt="image" src="https://user-images.githubusercontent.com/13253073/116536024-173f8680-a902-11eb-9e6e-387d8cb71e6a.png">
- Default URL(when no variation is selected)
    - Used when default url per product is not specified
- URL Template (use {#var#} for placeholder)
    - Product variation ASIN is replaced in place of {#var#} eg: https://amazon.in/dp/{#var#}

# Product settings
<img width="891" alt="image" src="https://user-images.githubusercontent.com/13253073/116536206-448c3480-a902-11eb-8fce-658e85697093.png">
- Product Specific Default link if no variation is selected and no global default link specific in plugin settings (product meta field: dynazon_default_link)

# Per Variation settings
<img width="937" alt="image" src="https://user-images.githubusercontent.com/13253073/116536286-5b328b80-a902-11eb-84c8-04e9f770fb0e.png">
- Asin from above field will be replaced in URL template 
