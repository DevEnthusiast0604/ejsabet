<template>
    <div class="loading-window">
        <div class="car">
            <div class="strike"></div>
            <div class="strike strike2"></div>
            <div class="strike strike3"></div>
            <div class="strike strike4"></div>
            <div class="strike strike5"></div>
            <div class="car-detail spoiler"></div>
            <div class="car-detail back"></div>
            <div class="car-detail center"></div>
            <div class="car-detail center1"></div>
            <div class="car-detail front"></div>
            <div class="car-detail wheel"></div>
            <div class="car-detail wheel wheel2"></div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'VLoading',
}
</script>
<style lang="sass" scoped>
    $border-color: #ffe4e1
    $background-color: #333
    $border-radius-big: 6px
    $font-size-header: 16px

    $car-color: $border-color
    $spin-deg: 36deg

    @keyframes spin
        0%
            transform: translate(2px, 1px) rotate($spin-deg * 0)
        10%
            transform: translate(-1px, -3px) rotate($spin-deg * 1)
        20%
            transform: translate(-2px, 0px) rotate($spin-deg * 2)
        30%
            transform: translate(1px, 2px) rotate($spin-deg * 3)
        40%
            transform: translate(1px, -1px) rotate($spin-deg * 4)
        50%
            transform: translate(-1px, 3px) rotate($spin-deg * 5)
        60%
            transform: translate(-1px, 1px) rotate($spin-deg * 6)
        70%
            transform: translate(3px, 1px) rotate($spin-deg * 7)
        80%
            transform: translate(-2px, -1px) rotate($spin-deg * 8)
        90%
            transform: translate(2px, 1px) rotate($spin-deg * 9)
        100%
            transform: translate(1px, -2px) rotate($spin-deg * 10)

    @keyframes speed
        0%
            transform: translate(2px, 1px) rotate(0deg)
        10%
            transform: translate(-1px, -3px) rotate(-1deg)
        20%
            transform: translate(-2px, 0px) rotate(1deg)
        30%
            transform: translate(1px, 2px) rotate(0deg)
        40%
            transform: translate(1px, -1px) rotate(1deg)
        50%
            transform: translate(-1px, 3px) rotate(-1deg)
        60%
            transform: translate(-1px, 1px) rotate(0deg)
        70%
            transform: translate(3px, 1px) rotate(-1deg)
        80%
            transform: translate(-2px, -1px) rotate(1deg)
        90%
            transform: translate(2px, 1px) rotate(0deg)
        100%
            transform: translate(1px, -2px) rotate(-1deg)

    @keyframes strikes
        from
            left: 25px
        to
            left: -80px
            opacity: 0

    @keyframes dots
        from
            width: 0px
        to
            width: 15px

    @keyframes fadeIn
        from
            opacity: 0
        to
            opacity: 1

    body
        background: $background-color
        font-family: "Lato", "Lucida Grande", "Lucida Sans Unicode", Tahoma, Sans-Serif

    .fadeIn
        animation: fadeIn .4s both

    .loading-window
        @extend .fadeIn

        position: relative
        color: $car-color
        height: 100px
        width: 300px
        margin: 0 auto
        scale: 0.65

        .text
            font-size: $font-size-header
            position: absolute
            width: auto
            top: 75%
            left: 38%
            margin: 0 auto
        .dots
            display: inline-block
            width: 5px
            overflow: hidden
            vertical-align: bottom
            animation: dots 1.5s linear infinite
            transition: 1

    .car
        position: absolute
        width: 117px
        height: 42px
        left: 92px
        top: 25px

        .strike
            position: absolute
            width: 11px
            height: 1px
            background: $car-color
            animation: strikes .2s linear infinite

        .strike2
            top: 11px
            animation-delay: .05s

        .strike3
            top: 22px
            animation-delay: .1s

        .strike4
            top: 33px
            animation-delay: .15s

        .strike5
            top: 44px
            animation-delay: .2s

        &-detail
            position: absolute
            display: block
            background: $car-color
            animation: speed .5s linear infinite

            &.spoiler
                width: 0
                height: 0
                top: 7px
                background: none
                border: 20px solid transparent
                border-bottom: 8px solid $car-color
                border-left: 20px solid $car-color

            &.back
                height: 20px
                width: 92px
                top: 15px
                left: 0px

            &.center
                height: 35px
                width: 75px
                left: 12px
                border-top-left-radius: 30px
                border-top-right-radius: 45px 40px
                border: 4px solid $car-color
                background: none
                box-sizing: border-box

            &.center1
                height: 35px
                width: 35px
                left: 12px
                border-top-left-radius: 30px

            &.front
                height: 20px
                width: 50px
                top: 15px
                left: 67px
                border-top-right-radius: 50px 40px
                border-bottom-right-radius: 10px

            &.wheel
                height: 20px
                width: 20px
                border-radius: 50%
                top: 20px
                left: 12px
                border: 3px solid $background-color
                background: linear-gradient(45deg, transparent 45%, $border-color 46%, $border-color 54%, transparent 55%), linear-gradient(-45deg, transparent 45%, $border-color 46%, $border-color 54%, transparent 55%), linear-gradient(90deg, transparent 45%, $border-color 46%, $border-color 54%, transparent 55%), linear-gradient(0deg, transparent 45%, $border-color 46%, $border-color 54%, transparent 55%), radial-gradient($border-color 29%, transparent 30%, transparent 50%, $border-color 51%),$background-color
                animation-name: spin

            &.wheel2
                left: 82px
</style>