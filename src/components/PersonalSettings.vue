<template>
    <div id="github_prefs" class="section">
            <h2>
                <a class="icon icon-github" :style="{'background-image': 'url(' + iconUrl + ')'}"></a>
                {{ t('github', 'Github') }}
            </h2>
            <div class="grid-form">
                <label for="github-token">
                    <a class="icon icon-category-auth"></a>
                    {{ t('github', 'Github access token') }}
                </label>
                <input id="github-token" type="password" v-model="state.token" @input="onInput"
                    :readonly="readonly"
                    @focus="readonly = false"
                    :placeholder="t('github', 'Get a token in Github settings')" />
                <button id="github-oauth" v-if="showOAuth" @click="onOAuthClick">
                    {{ t('github', 'Get access token with OAuth') }}
                </button>
            </div>
    </div>
</template>

<script>
import { loadState } from '@nextcloud/initial-state'
import { generateUrl, imagePath } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { delay } from '../utils'
import { showSuccess, showError } from '@nextcloud/dialogs'

export default {
    name: 'PersonalSettings',

    props: [],
    components: {
    },

    mounted() {
        const paramString = window.location.search.substr(1)
        const urlParams = new URLSearchParams(paramString)
        const ghToken = urlParams.get('githubToken')
        if (ghToken === 'success') {
            OC.dialogs.alert(
                t('github', 'Github OAuth access token successfully retieved!'),
                t('github', 'Success')
            )
        } else if (ghToken === 'error') {
            OC.dialogs.alert(
                t('github', 'Github OAuth access token could not be obtained:') + ' ' + urlParams.get('message'),
                t('github', 'Error')
            )
        }
    },

    data() {
        return {
            state: loadState('github', 'user-config'),
            iconUrl: imagePath('github', 'app.svg'),
            readonly: true,
        }
    },

    watch: {
    },

    computed: {
        showOAuth() {
            return this.state.client_id && this.state.client_secret
        },
    },

    methods: {
        onInput() {
            const that = this
            delay(function() {
                that.saveOptions()
            }, 2000)()
        },
        saveOptions() {
            const req = {
                values: {
                    token: this.state.token
                }
            }
            const url = generateUrl('/apps/github/config')
            axios.put(url, req)
                .then(function (response) {
                    showSuccess(t('github', 'Github options saved.'))
                })
                .catch(function (error) {
                    showError(t('github', 'Failed to save Github options') +
                        ': ' + error.response.request.responseText
                    )
                })
                .then(function () {
                })
        },
        onOAuthClick() {
            const redirect_endpoint = generateUrl('/apps/github/oauth-redirect')
            const redirect_uri = OC.getProtocol() + '://' + OC.getHostName() + redirect_endpoint
            const oauth_state = Math.random().toString(36).substring(3)
            const request_url = 'https://github.com/login/oauth/authorize?client_id=' + encodeURIComponent(this.state.client_id) +
                '&redirect_uri=' + encodeURIComponent(redirect_uri) +
                '&state=' + encodeURIComponent(oauth_state) +
                '&scope=' + encodeURIComponent('user repo notifications')

            const req = {
                values: {
                    oauth_state: oauth_state,
                }
            }
            const url = generateUrl('/apps/github/config')
            axios.put(url, req)
                .then(function (response) {
                    window.location.replace(request_url)
                })
                .catch(function (error) {
                    showError(t('github', 'Failed to save Github OAuth state') +
                        ': ' + error.response.request.responseText
                    )
                })
                .then(function () {
                })
        }
    }
}
</script>

<style scoped lang="scss">
.grid-form label {
    line-height: 38px;
}
.grid-form input {
    width: 100%;
}
.grid-form {
    width: 700px;
    display: grid;
    grid-template: 1fr / 1fr 1fr 1fr;
    margin-left: 30px;
}
#github_prefs .icon {
    display: inline-block;
    width: 32px;
}
#github_prefs .grid-form .icon {
    margin-bottom: -3px;
}
.icon-github {
    mix-blend-mode: difference;
    background-size: 23px 23px;
    height: 23px;
    margin-bottom: -4px;
}
</style>