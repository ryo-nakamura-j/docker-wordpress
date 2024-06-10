// https://raw.githubusercontent.com/alexjoverm/v-lazy-image/master/src/index.js
Vue.component('v-lazy-img', {
  props: {
    src: {
      type: String,
      required: true
    },
    srcPlaceholder: {
      type: String,
      default: ""
    },
    srcset: {
      type: String
    },
    intersectionOptions: {
      type: Object,
      default: function() {}
    }
  },
  data: function() { 
    return { 
      observer: null, 
      intersected: false, 
      loaded: false 
    };
  },
  computed: {
    srcImage: function() {
      return this.intersected ? this.src : this.srcPlaceholder;
    },
    srcsetImage: function() {
      return this.intersected && this.srcset ? this.srcset : false;
    }
  },
  render: function(h) {
    return h("img", {
      attrs: { src: this.srcImage, srcset: this.srcsetImage },
      class: {
        "v-lazy-image": true,
        "v-lazy-image-loaded": this.loaded
      }
    });
  },
  mounted: function() {
    var vm = this;
    this.$el.addEventListener("load", function(ev)  {
      if (vm.$el.getAttribute('src') !== vm.srcPlaceholder) {
        vm.loaded = true;
        vm.$emit("load");
      }
    });

    this.observer = new IntersectionObserver(function(entries) {
      var image = entries[0];
      if (image.isIntersecting) {
        vm.intersected = true;
        vm.observer.disconnect();
        vm.$emit("intersect");
      }
    }, this.intersectionOptions);

    this.observer.observe(this.$el);
  },
  destroyed: function() {
    this.observer.disconnect();
  }
});