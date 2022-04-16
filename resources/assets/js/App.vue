<template>
    <div class="main">
        <div class="navbar">
            <div class="navbar-inner">
                <div class="navbar-brand">
                    <div>
                        <img src="../img/apex-logo.png" class="navbar-logo">
                    </div>
                </div>
                <div class="navbar-right">
                    <user ref="more">
                        <strong slot="title">{{sharedState.user.name}}</strong>
                        <li>
                            <router-link to="/personal-settings">Personal Settings</router-link>
                        </li>
                        <li>
                            <a href="/logout">Logout</a>
                        </li>
                    </user>
                </div>
            </div>
        </div>
        <div class="main-content">
            <sidebar></sidebar>
            <div class="main-view">
                <transition name="fade" mode="out-in">
                    <router-view class="layout" :key="$route.path"></router-view>
                </transition>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Sidebar from './components/layouts/Sidebar.vue'
    import { User } from './components/dropdown'
    export default {
        data() {
            return {
                sharedState: window.apex
            }
        },
        components: {
            Sidebar,
            User,
        }
    }
</script>
