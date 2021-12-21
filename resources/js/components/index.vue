<template>
  <div class="">
    <carousel
      v-if="banners.length"
      :items="1"
      :loop="true"
      :autoplay="true"
      :nav="false"
      :dots="false"
      id="top_slider"
    >
      <img
        v-for="(row, i) in banners"
        :key="'banner' + i"
        class=""
      
        :src="row.imagepath"
        alt=""
        style="cursor: pointer;height:350px"
      />
    </carousel>
    <div class="container pt-4">
      <div class="home-product">
        <div class="item p-2" v-for="(row, i) in tags">
          <div class="card-sales">
            <img
              style="height: 200px"
              :src="row.imagepath"
              class="img-container"
            />
            <a :href="'/products/' + row.id" target="_self">
              <p class="caption">{{ row.name }}</p>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="owl-carousel owl-theme home-slider">
      <!-- <carousel :items="4" :loop="true" :autoplay="true" :nav="false" :dots="false" class=" owl-theme " :responsive="{0:{items: 1,stagePadding: 25},550: {items: 2},786: {items: 3},1000: {items: 3},1200: {items: 4}}" > -->
      <div v-for="(row, i) in categories" :key="'category' + i" class="item">
        <div class="img-container">
          <a :href="'/category/' + row.id" target="_self">
            <img :src="row.image" width="50" alt=""
          /></a>
        </div>
        <div class="">
          <a :href="'/category/' + row.id" target="_self">
            <h1
              class="text-center h5 mt-4"
              style="max-width: 80%; margin: 0 auto"
            >
              {{ row.name }}
            </h1></a
          >
        </div>
      </div>
      <!-- </carousel> -->
    </div>

    <div class="slide" v-for="(row, i) in data" :key="'data' + i">
      <div class="container">
        <div
          class="top-section d-flex justify-content-between align-items-center"
        >
          <h2 class="m-0 text-capitalize">{{ row.name }}</h2>
          <!-- <a href="" class="y-color text-capitalize"> view all
                        <i class="fas fa-chevron-right"></i>
                    </a> -->
        </div>
        <div class="owl-carousel owl-theme product-slider">
          <div
            class="item mx-2"
            v-for="(product, x) in row.products"
            :key="'product' + x"
          >
            <div class="g-card">
              <div class="card position-relative">
                <div class="img-wrap">
                  <img :src="product.image" :alt="product.name" />
                </div>
                <p class="description"> <a
                  :href="'/product/' + product.id"
                 class="y_color"
                  target="_self"
                  >{{ product.name }}</a></p>
                <span class="new">new</span>
                <span class="discount" v-if="product.discount > 0"
                  >{{ product.discount_precent }} %</span
                >
              </div>
              <div class="action">
                <p class="price m-0">
                  {{ product.price }} <span>{{ trans.home.currency }}</span>
                </p>
                <a
                  href="javascript:;"
                  v-if="product.in_wishlist == 0"
                  @click="addToWishlist(product)"
                  ><i class="y-color favorite_icon fa fa-star-o"></i
                ></a>
                <a
                  href="javascript:;"
                  v-if="product.in_wishlist == 1"
                  @click="deletefromwishlist(product)"
                  ><i class="y-color favorite_icon fa fa-star"></i
                ></a>
                <button
                  type="button"
                  @click="addToCart(product)"
                  class="y-btn"
                  v-if="product.has_options == false"
                >
                  {{ trans.home.buy_now }}
                </button>
                <a
                  :href="'/product/' + product.id"
                  class="y-btn"
                  v-else-if="product.has_options == true"
                  target="_self"
                  >{{ trans.home.choose }}</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import carousel from "v-owl-carousel";

export default {
  props: ["lang_id"],
  components: { carousel },
  data() {
    return {
      brands: [],
      categories: [],
      tags: [],
      banners: [],
      data: [],
      trans: window.trans,
    };
  },
  computed: {
    access_token() {
      return this.$store.state.user.token;
    },
  },

  created() {
    this.getCategories();
    this.getData();
    this.getTags();
  },
  methods: {
    getData() {
      let config = {};
      config.headers = { Authorization: `Bearer ${this.access_token}` };
      axios
        .get("/api/home-page?lang_id=" + this.lang_id, config)
        .then((res) => {
          this.brands = res.data.brands;
          this.banners = res.data.banners;
          this.data = res.data.data;
          this.$nextTick(() => {
            var rtlDirection = null; // detect direction of body
            $("body").hasClass("rtl")
              ? (rtlDirection = true)
              : (rtlDirection = false);

            $(".home-slider").owlCarousel({
              nav: true,
              dots: false,
              autoplay: true,
              autoplayTimeout: 2000,
              loop: true,
              rtl: rtlDirection,
              responsive: {
                0: {
                  items: 3,
                },
                550: {
                  items: 3,
                },
                786: {
                  items: 4,
                },
                1000: {
                  items: 5,
                },
                1200: {
                  items: 6,
                },
              },
            });

            $(".product-slider").owlCarousel({
              items: 1,
              nav: true,
              dots: false,
              autoplay: true,
              autoplayTimeout: 2000,
              loop: true,
              rtl: rtlDirection,
              responsive: {
                0: {
                  items: 2,
                  stagePadding: 25,
                },
                390: {
                  items: 1,
                  stagePadding: 50,
                },
                450: {
                  items: 2,
                },
                600: {
                  items: 3,
                },
                850: {
                  items: 4,
                },
                1000: {
                  items: 5,
                },
                1200: {
                  items: 5.4,
                },
              },
            });
          });
        });
    },
    getCategories() {
      axios.get("/api/main-categories?lang_id=" + this.lang_id).then((res) => {
        this.categories = res.data.data;
      });
    },
    getTags() {
      axios.get("/api/tags?lang_id=" + this.lang_id).then((res) => {
        this.tags = res.data.data;
      });
    },
    addToCart(product) {
      this.$store.dispatch("add", { product: product, value_id: null });
    },
    addToWishlist(product) {
      let config = {};
      config.headers = { Authorization: `Bearer ${this.access_token}` };
      console.log(this.access_token);
      if (this.access_token == null) {
        alert("Login First");
        return false;
      }

      axios
        .get("/api/wishlists/add?product_id=" + product.id, config)
        .then((res) => {
          alert("added Successfully");
          this.getData();
        });
    },
    deletefromwishlist(product) {
      let config = {};
      config.headers = { Authorization: `Bearer ${this.access_token}` };
      axios
        .get("/api/wishlists/delete?product_id=" + product.id, config)
        .then((res) => {
          alert("Deleted Successfully");
          this.getData();
        });
    },
  },
};
</script>
