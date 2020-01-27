<div class="swift-performance-notice update-nag swift-upgrade-notice" style="padding:25px 10px 10px 10px;position: relative;display: block;"><img src="<?php echo SWIFT_PERFORMANCE_URI;?>images/logo.png">
      <div class="swte-upgrade-container">
            <div id="countdown"></div>
            <h3>
                  <?php echo __('Summer Sale!', 'swift-performance');?><br>
                  <?php echo __('Upgrade Pro now with <strong>90% discount</strong> for the first year!', 'swift-performance');?>
            </h3>
            <div class="swte-upgrade-col">
                  <div class="swte-upgrade-header">
                        <h4>Single</h4>
                        <div class="swte-strikeout">$39<sup>.99</sup></div>
                        <div class="swte-upgrade-price"><sup>$</sup>3<span class="cents">.99</span></div>
                  </div>
                  <ul class="swte-upgrade-features">
                        <li class="strong"><?php esc_html_e('1 site', 'swift-performance');?></li>
                        <li><?php esc_html_e('Unlimited Image Optimizer', 'swift-performance');?></li>
                        <li><?php esc_html_e('Compute API', 'swift-performance');?></li>
                        <li><?php esc_html_e('Extended Support', 'swift-performance');?></li>
                        <li><?php esc_html_e('All Premium Features', 'swift-performance');?></li>
                  </ul>
                  <a href="https://swiftperformance.io/upgrade-pro/" target="_blank" class="swift-btn swift-btn-green"><?php esc_html_e('UPGRADE', 'swift-performance');?></a>
            </div>
            <div class="swte-upgrade-col featured">
                  <div class="swte-upgrade-header">
                        <h4>Multi</h4>
                        <div class="swte-strikeout">$99<sup>.99</sup></div>
                        <div class="swte-upgrade-price"><sup>$</sup>9<span class="cents">.99</span></div>
                  </div>
                  <ul class="swte-upgrade-features">
                        <li class="strong"><?php esc_html_e('4 sites', 'swift-performance');?></li>
                        <li><?php esc_html_e('Unlimited Image Optimizer', 'swift-performance');?></li>
                        <li><?php esc_html_e('Compute API', 'swift-performance');?></li>
                        <li><?php esc_html_e('Extended Support', 'swift-performance');?></li>
                        <li><?php esc_html_e('All Premium Features', 'swift-performance');?></li>
                  </ul>
                  <a href="https://swiftperformance.io/upgrade-pro/" target="_blank" class="swift-btn swift-btn-green"><?php esc_html_e('UPGRADE', 'swift-performance');?></a>
            </div>
            <div class="swte-upgrade-col">
                  <div class="swte-upgrade-header">
                        <h4>Developer</h4>
                        <div class="swte-strikeout">$199<sup>.99</sup></div>
                        <div class="swte-upgrade-price"><sup>$</sup>19<span class="cents">.99</span></div>
                  </div>
                  <ul class="swte-upgrade-features">
                        <li class="strong"><?php esc_html_e('Unlimited', 'swift-performance');?></li>
                        <li><?php esc_html_e('Unlimited Image Optimizer', 'swift-performance');?></li>
                        <li><?php esc_html_e('Compute API', 'swift-performance');?></li>
                        <li><?php esc_html_e('Extended Support', 'swift-performance');?></li>
                        <li><?php esc_html_e('All Premium Features', 'swift-performance');?></li>
                  </ul>
                  <a href="https://swiftperformance.io/upgrade-pro/" target="_blank" class="swift-btn swift-btn-green"><?php esc_html_e('UPGRADE', 'swift-performance');?></a>
            </div>
      </div>

      <div class="swift-notice-buttonset">
            <a href="<?php echo add_query_arg('swift-hide-ad', 1, admin_url())?>" class="swift-btn swift-btn-black"><?php esc_html_e('Hide this ad', 'swift-performance');?></a>
            <a href="<?php echo esc_url(add_query_arg('subpage', 'upgrade-pro', menu_page_url(SWIFT_PERFORMANCE_SLUG, false)));?>"><?php esc_html_e('Don\'t like these ads?', 'swift-performance');?></a>
      </div>
</div>

<script>
Vue.filter('zerofill', function (value) {
  //value = ( value < 0 ? 0 : value );
  return (value < 10 && value > -1 ? '0' : '') + value;
});

var Tracker = Vue.extend({
  template: `
  <span v-show="show" class="flip-clock__piece">
    <span class="flip-clock__card flip-card">
      <b class="flip-card__top">{{current | zerofill}}</b>
      <b class="flip-card__bottom" data-value="{{current | zerofill}}"></b>
      <b class="flip-card__back" data-value="{{previous | zerofill}}"></b>
      <b class="flip-card__back-bottom" data-value="{{previous | zerofill}}"></b>
    </span>
    <span class="flip-clock__slot">{{property}}</span>
  </span>`,
  props: ['property', 'time'],
  data: () => ({
    current: 0,
    previous: 0,
    show: false }),


  events: {
    time(newValue) {

      if (newValue[this.property] === undefined) {
        this.show = false;
        return;
      }

      var val = newValue[this.property];
      this.show = true;

      val = val < 0 ? 0 : val;

      if (val !== this.current) {

        this.previous = this.current;
        this.current = val;

        this.$el.classList.remove('flip');
        void this.$el.offsetWidth;
        this.$el.classList.add('flip');
      }

    } } });






var countdown = document.getElementById('countdown');

var Countdown = new Vue({

  el: countdown,

  template: `
  <div class="flip-clock" data-date="2017-02-11" @click="update">
    <tracker
      v-for="tracker in trackers"
      :property="tracker"
      :time="time"
      v-ref:trackers
    ></tracker>
  </div>
  `,

  props: ['date', 'callback'],

  data: () => ({
    time: {},
    i: 0,
    trackers: ['Days', 'Hours', 'Minutes', 'Seconds'] //'Random',
  }),

  components: {
    Tracker },


  beforeDestroy() {
    if (window['cancelAnimationFrame']) {
      cancelAnimationFrame(this.frame);
    }
  },

  watch: {
    'date': function (newVal) {
      this.setCountdown(newVal);
    } },


  ready() {
    if (window['requestAnimationFrame']) {
      this.setCountdown(this.date);
      this.callback = this.callback || function () {};
      this.update();
    }
  },

  methods: {

    setCountdown(date) {
        this.countdown = moment('2019-08-31 23:59:59', 'YYYY-MM-DD HH:mm:ss');
    },

    update() {
      this.frame = requestAnimationFrame(this.update.bind(this));
      if (this.i++ % 10) {return;}
      var t = moment(new Date());
      // Calculation adapted from https://www.sitepoint.com/build-javascript-countdown-timer-no-dependencies/
      if (this.countdown) {

        t = this.countdown.diff(t);

        //t = this.countdown.diff(t);//.getTime();
        //console.log(t);
        this.time.Days = Math.floor(t / (1000 * 60 * 60 * 24));
        this.time.Hours = Math.floor(t / (1000 * 60 * 60) % 24);
        this.time.Minutes = Math.floor(t / 1000 / 60 % 60);
        this.time.Seconds = Math.floor(t / 1000 % 60);
      } else {
        this.time.Days = undefined;
        this.time.Hours = t.hours() % 13;
        this.time.Minutes = t.minutes();
        this.time.Seconds = t.seconds();
      }

      this.time.Total = t;

      this.$broadcast('time', this.time);
      return this.time;
    } } });
</script>