<template>
  <div>
    <button
      type="button"
      class="btn m-0 p-1 shadow-none"
    >
      <!-- gotToLikeがtrueであれば animated, heartbeat, fast のclass属性がハートアイコンに付与される -->
        <!-- 以上はMDBootstrapに用意されたclass属性 -->
      <i class="fas fa-heart mr-1"
        :class="{'red-text':this.isLikedBy, 'animated heartBeat fast':this.gotToLike}"
        @click="clickLike"
      />
    </button>
    <!-- Bladeから渡されたいいね数が入ったプロパティ initialCountLikes を一旦セットする -->
    <!-- 数を増減させるため initialCountLikes は直接使わない -->
    {{ countLikes }}
  </div>
</template>

<script>
  export default {
    props: {
      initialIsLikedBy: {
        type: Boolean,
        default: false,
      },
      initialCountLikes: {
        type: Number,
        default: 0,
      },
      authorized: {
        type: Boolean,
        default: false,
      },
      endpoint: {
        type: String,
      },
    },
    data() {
      return {
        isLikedBy: this.initialIsLikedBy,
        countLikes: this.initialCountLikes,
        gotToLike: false,
      }
    },
    methods: {
      clickLike() {
        if (!this.authorized) {
          alert('いいね機能はログイン中のみ使用できます')
          return
        }

        // いいね済みかどうかを三項演算子で判定する
        this.isLikedBy
          ? this.unlike()
          : this.like()
      },
      // asyncとawaitはJSで非同期通信を簡単に書くための仕組み
      // axiosはHTTP通信を行うためのJSをのライブラリ
      async like() {
        const response = await axios.put(this.endpoint)

        this.isLikedBy = true
        this.countLikes = response.data.countLikes
        this.gotToLike = true
      },
      async unlike() {
        const response = await axios.delete(this.endpoint)

        this.isLikedBy = false
        this.countLikes = response.data.countLikes
        this.gotToLike = false
      },
    },
  }
</script>