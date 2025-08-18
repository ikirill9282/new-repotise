@extends('layouts.site')

@section('content')
    <x-profile.wrap>

    <div class="create-product-container">
        <h1 class="createArticle__title">Create Product - Details</h1>
        <ul class="createArticle__breadCrumbs">
            <li class="createArticle__breadcrumb"><a href="">My Account</a></li>
            <li class="createArticle__breadcrumb"><a href="">My Products</a></li>
            <li class="createArticle__breadcrumb"><a href=""> Create Product (1/2)</a></li>
        </ul>
        <h4 class="createProduct__subtitle">Product Details</h4>
        <form action="" class="createProduct__productsDetails full-width-form">
            <label for="productTitle" class="createArticle-label createProduct__productTitle-label">Product Title</label>
            <div class="input-wrapper">
                <input id="productTitle" class="createProduct__productTitle-input input" type="text"
                    placeholder="Product Title" />
            </div>
            <label for="" class="createArticle-label createProduct__productType-label">Product Type</label>
            <div class="input-wrapper select">
                <input type="text" class="createProduct__productType-select select input" placeholder="Travel Guides"
                    id="tags-input" />
                <div class="tags-dropdown" id="product-type-dropdown">
                    <div class="tags-dropdown__item">beach</div>
                    <div class="tags-dropdown__item">budget</div>
                    <div class="tags-dropdown__item">family</div>
                    <div class="tags-dropdown__item">food</div>
                    <div class="tags-dropdown__item">hiking</div>
                    <div class="tags-dropdown__item">luxury</div>
                    <div class="tags-dropdown__item">nature</div>
                    <div class="tags-dropdown__item">photography</div>
                </div>
            </div>
            <ul class="createArticle__tags-list" id="product-type-tags">
                <li class="createArticle__tags-item">travel</li>
                <li class="createArticle__tags-item">adventure</li>
                <li class="createArticle__tags-item">europe</li>
                <li class="createArticle__tags-item">tips</li>
                <li class="createArticle__tags-item">mountains</li>
            </ul>
        
            <input id="checkbox-Subscription" type="checkbox" class="createProduct__subscription-checkbox" />
            <label for="checkbox-Subscription"
                class="createArticle-label createProduct__subscription-label">Subscription</label>
            <div class="checkbox-hidden">
                <label for="" class="createArticle-label createProduct__duration-label">Subscription Duration</label>
                <div class="input-wrapper select">
                    <select name="" id="" class="createProduct__duration-select select input">
                        <option value="" selected hidden>Annually</option>
                        <option value="">1</option>
                        <option value="">2</option>
                        <option value="">3</option>
                    </select>
                </div>
                <p class="createProduct__discounts">Prepayment Discounts:</p>
                <div class="createProduct__discounts-inputs">
                    <div class="input-group">
                        <label for="Month" class="createProduct__discounts-label createArticle-label">Month:</label>
                        <input id="Month" type="text" class="input createProduct__discounts-input" placeholder="%" />
                    </div>
        
                    <div class="input-group">
                        <label for="Quarter" class="createProduct__discounts-label createArticle-label">Quarter:</label>
                        <input id="Quarter" type="text" class="input createProduct__discounts-input" placeholder="%" />
                    </div>
        
                    <div class="input-group">
                        <label for="year" class="createProduct__discounts-label createArticle-label">Year:</label>
                        <input id="year" type="text" class="input createProduct__discounts-input" placeholder="%" />
                    </div>
                </div>
            </div>
            <label for="" class="createArticle-label">Product Description:</label>
            <div class="tiptap-container">
                <div class="tiptap-toolbar">
                    <button type="button" class="tiptap-button" id="desc-bold-btn" title="Жирный">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_3392_161852)">
                                <path
                                    d="M17.954 10.6631C18.6096 9.60392 18.9702 8.38871 18.9982 7.14336C19.0263 5.89801 18.7208 4.66781 18.1135 3.58021C17.5062 2.4926 16.6192 1.58716 15.5443 0.95768C14.4693 0.3282 13.2457 -0.00241883 12 7.50398e-05H5C4.46957 7.50398e-05 3.96086 0.210789 3.58579 0.585862C3.21071 0.960934 3 1.46964 3 2.00008V22.0001C3 22.5305 3.21071 23.0392 3.58579 23.4143C3.96086 23.7894 4.46957 24.0001 5 24.0001H15C16.5934 24.0018 18.1397 23.4593 19.3828 22.4625C20.6259 21.4657 21.4914 20.0742 21.8359 18.5185C22.1805 16.9628 21.9835 15.3361 21.2776 13.9075C20.5717 12.479 19.3991 11.3344 17.954 10.6631ZM7 4.00007H12C12.7956 4.00007 13.5587 4.31615 14.1213 4.87875C14.6839 5.44136 15 6.20443 15 7.00007C15 7.79572 14.6839 8.55879 14.1213 9.1214C13.5587 9.684 12.7956 10.0001 12 10.0001H7V4.00007ZM15 20.0001H7V14.0001H15C15.7956 14.0001 16.5587 14.3161 17.1213 14.8788C17.6839 15.4414 18 16.2044 18 17.0001C18 17.7957 17.6839 18.5588 17.1213 19.1214C16.5587 19.684 15.7956 20.0001 15 20.0001Z"
                                    fill="#212121" />
                            </g>
                            <defs>
                                <clipPath id="clip0_3392_161852">
                                    <rect width="24" height="24" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                    </button>
                    <button type="button" class="tiptap-button" id="desc-align-justify-btn" title="Выровнять по ширине">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1 6H23C23.2652 6 23.5196 5.89464 23.7071 5.7071C23.8946 5.51957 24 5.26521 24 5C24 4.73478 23.8946 4.48043 23.7071 4.29289C23.5196 4.10536 23.2652 4 23 4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26521 0.105357 5.51957 0.292893 5.7071C0.48043 5.89464 0.734784 6 1 6Z"
                                fill="#212121" />
                            <path
                                d="M23 9H1C0.734784 9 0.48043 9.10536 0.292893 9.29289C0.105357 9.48043 0 9.73478 0 10C0 10.2652 0.105357 10.5196 0.292893 10.7071C0.48043 10.8946 0.734784 11 1 11H23C23.2652 11 23.5196 10.8946 23.7071 10.7071C23.8946 10.5196 24 10.2652 24 10C24 9.73478 23.8946 9.48043 23.7071 9.29289C23.5196 9.10536 23.2652 9 23 9Z"
                                fill="#212121" />
                            <path
                                d="M23 19H1C0.734784 19 0.48043 19.1054 0.292893 19.2929C0.105357 19.4804 0 19.7348 0 20C0 20.2652 0.105357 20.5196 0.292893 20.7071C0.48043 20.8947 0.734784 21 1 21H23C23.2652 21 23.5196 20.8947 23.7071 20.7071C23.8946 20.5196 24 20.2652 24 20C24 19.7348 23.8946 19.4804 23.7071 19.2929C23.5196 19.1054 23.2652 19 23 19Z"
                                fill="#212121" />
                            <path
                                d="M23 14H1C0.734784 14 0.48043 14.1054 0.292893 14.2929C0.105357 14.4804 0 14.7348 0 15C0 15.2652 0.105357 15.5196 0.292893 15.7071C0.48043 15.8947 0.734784 16 1 16H23C23.2652 16 23.5196 15.8947 23.7071 15.7071C23.8946 15.5196 24 15.2652 24 15C24 14.7348 23.8946 14.4804 23.7071 14.2929C23.5196 14.1054 23.2652 14 23 14Z"
                                fill="#212121" />
                        </svg>
                    </button>
                    <button type="button" class="tiptap-button" id="desc-align-left-btn" title="Выровнять по левому краю">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1 6H23C23.2652 6 23.5196 5.89464 23.7071 5.7071C23.8946 5.51957 24 5.26521 24 5C24 4.73478 23.8946 4.48043 23.7071 4.29289C23.5196 4.10536 23.2652 4 23 4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26521 0.105357 5.51957 0.292893 5.7071C0.48043 5.89464 0.734784 6 1 6Z"
                                fill="#212121" />
                            <path
                                d="M1 11H15C15.2652 11 15.5196 10.8946 15.7071 10.7071C15.8946 10.5196 16 10.2652 16 10C16 9.73478 15.8946 9.48043 15.7071 9.29289C15.5196 9.10536 15.2652 9 15 9H1C0.734784 9 0.48043 9.10536 0.292893 9.29289C0.105357 9.48043 0 9.73478 0 10C0 10.2652 0.105357 10.5196 0.292893 10.7071C0.48043 10.8946 0.734784 11 1 11Z"
                                fill="#212121" />
                            <path
                                d="M15 19H1C0.734784 19 0.48043 19.1054 0.292893 19.2929C0.105357 19.4804 0 19.7348 0 20C0 20.2652 0.105357 20.5196 0.292893 20.7071C0.48043 20.8947 0.734784 21 1 21H15C15.2652 21 15.5196 20.8947 15.7071 20.7071C15.8946 20.5196 16 20.2652 16 20C16 19.7348 15.8946 19.4804 15.7071 19.2929C15.5196 19.1054 15.2652 19 15 19Z"
                                fill="#212121" />
                            <path
                                d="M23 14H1C0.734784 14 0.48043 14.1054 0.292893 14.2929C0.105357 14.4804 0 14.7348 0 15C0 15.2652 0.105357 15.5196 0.292893 15.7071C0.48043 15.8947 0.734784 16 1 16H23C23.2652 16 23.5196 15.8947 23.7071 15.7071C23.8946 15.5196 24 15.2652 24 15C24 14.7348 23.8946 14.4804 23.7071 14.2929C23.5196 14.1054 23.2652 14 23 14Z"
                                fill="#212121" />
                        </svg>
                    </button>
                    <button type="button" class="tiptap-button" id="desc-align-center-btn" title="Выровнять по центру">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1 6H23C23.2652 6 23.5196 5.89464 23.7071 5.7071C23.8946 5.51957 24 5.26521 24 5C24 4.73478 23.8946 4.48043 23.7071 4.29289C23.5196 4.10536 23.2652 4 23 4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26521 0.105357 5.51957 0.292893 5.7071C0.48043 5.89464 0.734784 6 1 6Z"
                                fill="#212121" />
                            <path
                                d="M5 9C4.73478 9 4.48043 9.10536 4.29289 9.29289C4.10536 9.48043 4 9.73478 4 10C4 10.2652 4.10536 10.5196 4.29289 10.7071C4.48043 10.8946 4.73478 11 5 11H19C19.2652 11 19.5196 10.8946 19.7071 10.7071C19.8946 10.5196 20 10.2652 20 10C20 9.73478 19.8946 9.48043 19.7071 9.29289C19.5196 9.10536 19.2652 9 19 9H5Z"
                                fill="#212121" />
                            <path
                                d="M19 19H5C4.73478 19 4.48043 19.1054 4.29289 19.2929C4.10536 19.4804 4 19.7348 4 20C4 20.2652 4.10536 20.5196 4.29289 20.7071C4.48043 20.8947 4.73478 21 5 21H19C19.2652 21 19.5196 20.8947 19.7071 20.7071C19.8946 20.5196 20 20.2652 20 20C20 19.7348 19.8946 19.4804 19.7071 19.2929C19.5196 19.1054 19.2652 19 19 19Z"
                                fill="#212121" />
                            <path
                                d="M23 14H1C0.734784 14 0.48043 14.1054 0.292893 14.2929C0.105357 14.4804 0 14.7348 0 15C0 15.2652 0.105357 15.5196 0.292893 15.7071C0.48043 15.8947 0.734784 16 1 16H23C23.2652 16 23.5196 15.8947 23.7071 15.7071C23.8946 15.5196 24 15.2652 24 15C24 14.7348 23.8946 14.4804 23.7071 14.2929C23.5196 14.1054 23.2652 14 23 14Z"
                                fill="#212121" />
                        </svg>
                    </button>
                    <button type="button" class="tiptap-button" id="desc-align-right-btn"
                        title="Выровнять по правому краю">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1 6H23C23.2652 6 23.5196 5.89464 23.7071 5.7071C23.8946 5.51957 24 5.26521 24 5C24 4.73478 23.8946 4.48043 23.7071 4.29289C23.5196 4.10536 23.2652 4 23 4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26521 0.105357 5.51957 0.292893 5.7071C0.48043 5.89464 0.734784 6 1 6Z"
                                fill="#212121" />
                            <path
                                d="M23 9H9C8.73478 9 8.48043 9.10536 8.29289 9.29289C8.10536 9.48043 8 9.73478 8 10C8 10.2652 8.10536 10.5196 8.29289 10.7071C8.48043 10.8946 8.73478 11 9 11H23C23.2652 11 23.5196 10.8946 23.7071 10.7071C23.8946 10.5196 24 10.2652 24 10C24 9.73478 23.8946 9.48043 23.7071 9.29289C23.5196 9.10536 23.2652 9 23 9Z"
                                fill="#212121" />
                            <path
                                d="M23 19H9C8.73478 19 8.48043 19.1054 8.29289 19.2929C8.10536 19.4804 8 19.7348 8 20C8 20.2652 8.10536 20.5196 8.29289 20.7071C8.48043 20.8947 8.73478 21 9 21H23C23.2652 21 23.5196 20.8947 23.7071 20.7071C23.8946 20.5196 24 20.2652 24 20C24 19.7348 23.8946 19.4804 23.7071 19.2929C23.5196 19.1054 23.2652 19 23 19Z"
                                fill="#212121" />
                            <path
                                d="M23 14H1C0.734784 14 0.48043 14.1054 0.292893 14.2929C0.105357 14.4804 0 14.7348 0 15C0 15.2652 0.105357 15.5196 0.292893 15.7071C0.48043 15.8947 0.734784 16 1 16H23C23.2652 16 23.5196 15.8947 23.7071 15.7071C23.8946 15.5196 24 15.2652 24 15C24 14.7348 23.8946 14.4804 23.7071 14.2929C23.5196 14.1054 23.2652 14 23 14Z"
                                fill="#212121" />
                        </svg>
                    </button>
                    <button type="button" class="tiptap-button" id="desc-indent-btn" title="Увеличить отступ">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1.03516 6.0003H23C23.2652 6.0003 23.5196 5.89494 23.7071 5.70741C23.8946 5.51987 24 5.26552 24 5.0003C24 4.73509 23.8946 4.48073 23.7071 4.2932C23.5196 4.10566 23.2652 4.00031 23 4.00031H1.03516C0.769944 4.00031 0.51559 4.10566 0.328053 4.2932C0.140517 4.48073 0.0351562 4.73509 0.0351562 5.0003C0.0351562 5.26552 0.140517 5.51987 0.328053 5.70741C0.51559 5.89494 0.769944 6.0003 1.03516 6.0003Z"
                                fill="#212121" />
                            <path
                                d="M23 9H9C8.73478 9 8.48043 9.10536 8.29289 9.29289C8.10536 9.48043 8 9.73478 8 10C8 10.2652 8.10536 10.5196 8.29289 10.7071C8.48043 10.8946 8.73478 11 9 11H23C23.2652 11 23.5196 10.8946 23.7071 10.7071C23.8946 10.5196 24 10.2652 24 10C24 9.73478 23.8946 9.48043 23.7071 9.29289C23.5196 9.10536 23.2652 9 23 9Z"
                                fill="#212121" />
                            <path
                                d="M23 19.0003H1.03516C0.769944 19.0003 0.51559 19.1057 0.328053 19.2932C0.140517 19.4807 0.0351562 19.7351 0.0351562 20.0003C0.0351562 20.2655 0.140517 20.5199 0.328053 20.7074C0.51559 20.895 0.769944 21.0003 1.03516 21.0003H23C23.2652 21.0003 23.5196 20.895 23.7071 20.7074C23.8946 20.5199 24 20.2655 24 20.0003C24 19.7351 23.8946 19.4807 23.7071 19.2932C23.5196 19.1057 23.2652 19.0003 23 19.0003Z"
                                fill="#212121" />
                            <path
                                d="M23 13.9997H9C8.73478 13.9997 8.48043 14.1051 8.29289 14.2926C8.10536 14.4801 8 14.7345 8 14.9997C8 15.2649 8.10536 15.5193 8.29289 15.7068C8.48043 15.8943 8.73478 15.9997 9 15.9997H23C23.2652 15.9997 23.5196 15.8943 23.7071 15.7068C23.8946 15.5193 24 15.2649 24 14.9997C24 14.7345 23.8946 14.4801 23.7071 14.2926C23.5196 14.1051 23.2652 13.9997 23 13.9997Z"
                                fill="#212121" />
                            <path
                                d="M1.707 16.2449L4.681 13.2709C4.88508 13.0662 4.99968 12.7889 4.99968 12.4999C4.99968 12.2108 4.88508 11.9336 4.681 11.7289L1.707 8.75488C1.56709 8.61501 1.38883 8.51979 1.19479 8.48125C1.00074 8.44272 0.799624 8.4626 0.616884 8.5384C0.434143 8.61419 0.27799 8.74249 0.168182 8.90705C0.0583736 9.07161 -0.00015534 9.26505 3.09641e-07 9.46288V15.5369C-0.00015534 15.7347 0.0583736 15.9282 0.168182 16.0927C0.27799 16.2573 0.434143 16.3856 0.616884 16.4614C0.799624 16.5372 1.00074 16.5571 1.19479 16.5185C1.38883 16.48 1.56709 16.3848 1.707 16.2449Z"
                                fill="#212121" />
                        </svg>
                    </button>
                    <button type="button" class="tiptap-button" id="desc-list-ol-btn" title="Нумерованный список">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_3392_161855)">
                                <path
                                    d="M4.00078 6.00012C3.60654 6.00115 3.216 5.924 2.85176 5.77312C2.48752 5.62224 2.15682 5.40063 1.87878 5.12112L0.334784 3.74712C0.136667 3.57048 0.0168323 3.32238 0.00164182 3.05739C-0.0135486 2.7924 0.0771495 2.53223 0.253784 2.33412C0.430418 2.136 0.678519 2.01616 0.943508 2.00097C1.2085 1.98578 1.46867 2.07648 1.66678 2.25312L3.25178 3.66712C3.34252 3.76901 3.45315 3.85126 3.57686 3.90881C3.70057 3.96635 3.83475 3.99797 3.97114 4.00171C4.10753 4.00546 4.24324 3.98126 4.36993 3.93059C4.49661 3.87993 4.61159 3.80388 4.70778 3.70712L8.31178 0.276116C8.50558 0.103586 8.75884 0.0131348 9.01809 0.0238668C9.27733 0.0345988 9.52225 0.145674 9.70113 0.333631C9.88 0.521588 9.97881 0.77171 9.97669 1.03117C9.97458 1.29062 9.8717 1.5391 9.68978 1.72412L6.10078 5.13812C5.82456 5.41273 5.49691 5.63021 5.13658 5.77812C4.77624 5.92603 4.39029 6.00147 4.00078 6.00012ZM24.0008 4.00012C24.0008 3.7349 23.8954 3.48055 23.7079 3.29301C23.5204 3.10547 23.266 3.00012 23.0008 3.00012H13.0008C12.7356 3.00012 12.4812 3.10547 12.2937 3.29301C12.1061 3.48055 12.0008 3.7349 12.0008 4.00012C12.0008 4.26533 12.1061 4.51969 12.2937 4.70722C12.4812 4.89476 12.7356 5.00012 13.0008 5.00012H23.0008C23.266 5.00012 23.5204 4.89476 23.7079 4.70722C23.8954 4.51969 24.0008 4.26533 24.0008 4.00012ZM6.10078 13.1381L9.68978 9.72412C9.78996 9.63494 9.87116 9.52652 9.92858 9.40531C9.98599 9.2841 10.0184 9.15259 10.024 9.01858C10.0295 8.88458 10.0081 8.75083 9.96087 8.62529C9.91367 8.49976 9.8417 8.385 9.74924 8.28784C9.65678 8.19069 9.54573 8.11312 9.42268 8.05977C9.29963 8.00641 9.26711 7.97835 9.033 7.97725C8.89888 7.97616 8.76592 8.00206 8.64202 8.0534C8.51812 8.10474 8.40581 8.18048 8.31178 8.27612L4.71178 11.7071C4.52142 11.8891 4.26818 11.9907 4.00478 11.9907C3.74139 11.9907 3.48815 11.8891 3.29778 11.7071L1.70778 10.1221C1.51918 9.93996 1.26658 9.83916 1.00438 9.84144C0.742186 9.84372 0.491373 9.94889 0.305965 10.1343C0.120557 10.3197 0.0153881 10.5705 0.0131097 10.8327C0.0108312 11.0949 0.111626 11.3475 0.293784 11.5361L1.87878 13.1211C2.43846 13.6809 3.1967 13.9968 3.98828 14C4.77986 14.0032 5.54063 13.6934 6.10478 13.1381H6.10078ZM24.0008 12.0001C24.0008 11.7349 23.8954 11.4805 23.7079 11.293C23.5204 11.1055 23.266 11.0001 23.0008 11.0001H13.0008C12.7356 11.0001 12.4812 11.1055 12.2937 11.293C12.1061 11.4805 12.0008 11.7349 12.0008 12.0001C12.0008 12.2653 12.1061 12.5197 12.2937 12.7072C12.4812 12.8948 12.7356 13.0001 13.0008 13.0001H23.0008C23.266 13.0001 23.5204 12.8948 23.7079 12.7072C23.8954 12.5197 24.0008 12.2653 24.0008 12.0001ZM6.10078 21.1381L9.68578 17.7241C9.78596 17.6349 9.87116 17.5265 9.92858 17.4053C9.98599 17.2841 10.0184 17.1526 10.02 17.0186C10.0255 16.8846 10.0041 16.7508 9.95687 16.6253C9.91367 16.4998 9.8417 16.385 9.74924 16.2878C9.65678 16.1907 9.54573 16.1131 9.42268 16.0598C9.29963 16.0064 9.26711 15.9783 9.033 15.9773C8.89888 15.9762 8.76592 16.0021 8.64202 16.0534C8.51812 16.1047 8.40581 16.1805 8.30778 16.2761L4.70778 19.7071C4.61159 19.8039 4.49661 19.8799 4.36993 19.9306C4.24324 19.9813 4.10753 20.0055 3.97114 20.0017C3.83475 19.998 3.70057 19.9664 3.57686 19.9088C3.45315 19.8513 3.34252 19.769 3.25178 19.6671L1.66678 18.2531C1.46867 18.0765 1.2085 17.9858 0.943508 18.001C0.678519 18.0162 0.430418 18.136 0.253784 18.3341C0.0771495 18.5322 -0.0135486 18.7924 0.00164182 19.0574C0.0168323 19.3224 0.136667 19.5705 0.334784 19.7471L1.87878 21.1211C2.43846 21.6809 3.1967 21.9968 3.98828 22C4.77986 22.0032 5.54063 21.6934 6.10478 21.1381H6.10078ZM24.0008 20.0001C24.0008 19.7349 23.8954 19.4805 23.7079 19.293C23.5204 19.1055 23.266 19.0001 23.0008 19.0001H13.0008C12.7356 19.0001 12.4812 19.1055 12.2937 19.293C12.1061 19.4805 12.0008 19.7349 12.0008 20.0001C12.0008 20.2653 12.1061 20.5197 12.2937 20.7072C12.4812 20.8948 12.7356 21.0001 13.0008 21.0001H23.0008C23.266 21.0001 23.5204 20.8948 23.7079 20.7072C23.8954 20.5197 24.0008 20.2653 24.0008 20.0001Z"
                                    fill="#212121" />
                            </g>
                            <defs>
                                <clipPath id="clip0_3392_161855">
                                    <rect width="24" height="24" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                    </button>
                    <button type="button" class="tiptap-button" id="desc-list-ul-btn" title="Маркированный список">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M7 6.0003H23C23.2652 6.0003 23.5196 5.89494 23.7071 5.70741C23.8946 5.51987 24 5.26552 24 5.0003C24 4.73509 23.8946 4.48073 23.7071 4.2932C23.5196 4.10566 23.2652 4.00031 23 4.00031H7C6.73478 4.00031 6.48043 4.10566 6.29289 4.2932C6.10536 4.48073 6 4.73509 6 5.0003C6 5.26552 6.10536 5.51987 6.29289 5.70741C6.48043 5.89494 6.73478 6.0003 7 6.0003Z"
                                fill="#212121" />
                            <path
                                d="M23 10.9997H7C6.73478 10.9997 6.48043 11.1051 6.29289 11.2926C6.10536 11.4801 6 11.7345 6 11.9997C6 12.2649 6.10536 12.5193 6.29289 12.7068C6.48043 12.8943 6.73478 12.9997 7 12.9997H23C23.2652 12.9997 23.5196 12.8943 23.7071 12.7068C23.8946 12.5193 24 12.2649 24 11.9997C24 11.7345 23.8946 11.4801 23.7071 11.2926C23.5196 11.1051 23.2652 10.9997 23 10.9997Z"
                                fill="#212121" />
                            <path
                                d="M23 18H7C6.73478 18 6.48043 18.1054 6.29289 18.2929C6.10536 18.4804 6 18.7348 6 19C6 19.2652 6.10536 19.5196 6.29289 19.7071C6.48043 19.8947 6.73478 20 7 20H23C23.2652 20 23.5196 19.8947 23.7071 20.7071C23.8946 20.5196 24 20.2652 24 20C24 19.7348 23.8946 19.4804 23.7071 19.2929C23.5196 19.1054 23.2652 18 23 18Z"
                                fill="#212121" />
                            <path
                                d="M2 6.99999C3.10457 6.99999 4 6.10456 4 5C4 3.89543 3.10457 3 2 3C0.89543 3 0 3.89543 0 5C0 6.10456 0.89543 6.99999 2 6.99999Z"
                                fill="#212121" />
                            <path
                                d="M2 14.0003C3.10457 14.0003 4 13.1049 4 12.0003C4 10.8957 3.10457 10.0003 2 10.0003C0.89543 10.0003 0 10.8957 0 12.0003C0 13.1049 0.89543 14.0003 2 14.0003Z"
                                fill="#212121" />
                            <path
                                d="M2 20.9997C3.10457 20.9997 4 20.1043 4 18.9997C4 17.8951 3.10457 16.9997 2 16.9997C0.89543 16.9997 0 17.8951 0 18.9997C0 20.1043 0.89543 20.9997 2 20.9997Z"
                                fill="#212121" />
                        </svg>
                    </button>
                </div>
                <div class="input-wrapper">
                    <div id="tiptap-editor-desc" class="tiptap-editor" contenteditable="true">
                        <p>Start writing your article here...</p>
                    </div>
                </div>
            </div>
            <!-- Скрытое поле для отправки данных -->
            <textarea id="product-description" name="description" class="visually-hidden"></textarea>
            <p class="createProduct__discounts">Product Description</p>
            <label for="" class="createArticle-label createProduct__location-label">location</label>
        
            <div class="input-wrapper search">
                <input type="text" class="createProduct__location-input search input"
                    placeholder="Enter city or country..." id="location-input" />
                <div class="tags-dropdown" id="location-dropdown">
                    <div class="tags-dropdown__item">Paris, France</div>
                    <div class="tags-dropdown__item">Tokyo, Japan</div>
                    <div class="tags-dropdown__item">New York, USA</div>
                    <div class="tags-dropdown__item">London, UK</div>
                    <div class="tags-dropdown__item">Rome, Italy</div>
                    <div class="tags-dropdown__item">Spain</div>
                    <div class="tags-dropdown__item">Germany</div>
                    <div class="tags-dropdown__item">Canada</div>
                    <div class="tags-dropdown__item">Sydney, Australia</div>
                    <div class="tags-dropdown__item">Cairo, Egypt</div>
                </div>
            </div>
            <ul class="createProduct__location-tags createArticle__tags-list" id="location-tags">
                <li class="createProduct__location-tag createArticle__tags-item">Paris, France</li>
                <li class="createProduct__location-tag createArticle__tags-item">Tokyo, Japan</li>
                <li class="createProduct__location-tag createArticle__tags-item">Spain</li>
            </ul>
            <label for="" class="createArticle-label createProduct__location-label">Categories</label>
            <div class="input-wrapper search">
                <input type="text" class="createProduct__location-input search input"
                    placeholder="Search or create categories...(Up to 5)" id="categories-input" />
                <div class="tags-dropdown" id="categories-dropdown">
                    <div class="tags-dropdown__item">Canada</div>
                    <div class="tags-dropdown__item">Sydney, Australia</div>
                    <div class="tags-dropdown__item">Cairo, Egypt</div>
                </div>
            </div>
            <ul class="createProduct__location-tags createArticle__tags-list" id="categories-tags">
                <li class="createProduct__location-tag createArticle__tags-item">name tag</li>
                <li class="createProduct__location-tag createArticle__tags-item">name tag</li>
                <li class="createProduct__location-tag createArticle__tags-item">name tag</li>
                <li class="createProduct__location-tag createArticle__tags-item">name tag</li>
                <li class="createProduct__location-tag createArticle__tags-item">name tag</li>
            </ul>
        </form>
        <h4 class="createProduct__subtitle">Pricing & SEO</h4>
        
        <form action="" class="createProduct__productsDetails createProduct__price">
            <div class="price-group">
                <label class="createArticle-label createProduct__price-label">Price</label>
                <input type="text" class="createProduct__price-input input" placeholder="$10" />
            </div>
        
            <div class="price-group">
                <label class="createArticle-label createProduct__price-label">Sale Price</label>
                <input type="text" class="createProduct__price-input input" placeholder="$10" />
            </div>
        </form>
        <form action="" class="createArticle__form createArticle__form-seo">
            <label for="metaTitle" class="createArticle-label createArticle-label createArticle__metaTitle-label">Meta
                Title:</label>
            <div class="input-wrapper">
                <input id="metaTitle" type="text" class="createArticle__input-metaTitle input createArticle__input"
                    placeholder="Enter meta title (for search engines)." />
            </div>
            <label for="metaDescription" class="createArticle-label createArticle__metaDescription-label">Meta
                Description:</label>
            <div class="input-wrapper">
                <textarea id="metaDescription" type="text"
                    class="createArticle__input-metaDescription input createArticle__input">Enter meta title (for search engines).</textarea>
            </div>
        </form>
        <div class="createArticle__buttons">
            <button class="createArticle__button-save">Save as Draft</button>
            <button class="createArticle__button-publish main-btn">Save & Continue</button>
        </div>
    </div >

  </x-profile.wrap>
@endsection
