<template>
  <div>
    <button
      class="btn-sm shadow-none border border-primary p-2"
      :class="buttonColor"
      @click="clickFollow"
    >
      <i
        class="mr-1"
        :class="buttonIcon"
      ></i>
      {{ buttonText }}
    </button>
  </div>
</template>

<script>
  export default {
    props: {
      initialIsFollowedBy: {
        type: Boolean,
        default: false,
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
        // プロパティ initialIsFollowedBy に渡された値をそのまま isFollowedBy にセットする
        isFollowedBy: this.initialIsFollowedBy,
      }
    },
    // isFollowedByの状態に応じて、ボタンの色、アイコン、テキストを変える
    computed: {
      buttonColor() {
        return this.isFollowedBy
          ? 'bg-primary text-white'
          : 'bg-white'
      },
      buttonIcon() {
        return this.isFollowedBy
          ? 'fas fa-user-check'
          : 'fas fa-user-plus'
      },
      buttonText() {
        return this.isFollowedBy
          ? 'フォロー中'
          : 'フォロー'
      },
    },

    methods: {

      // クリックされたときに実行
      clickFollow() {
        if (!this.authorized) {
          alert('フォロー機能はログイン中のみ使用できます')
          return
        }

        this.isFollowedBy
          ? this.unfollow()
          : this.follow()
      },
      async follow() {
        // this.endpoint、つまり users/{name}/followに対してPUTメソッドでリクエスト
        const response = await axios.put(this.endpoint)

        this.isFollowedBy = true
      },
      async unfollow() {
        // 同上に対してDELETEメソッドでリクエスト
        const response = await axios.delete(this.endpoint)

        this.isFollowedBy = false
      },
    },
  }
</script>>