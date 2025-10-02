<template>
    <div class="card flex justify-center">
        <Button v-tooltip.top="'Share'" rounded icon="pi pi-share-alt" severity="secondary" size="large"
            @click="visible = true"
            class="p-button-text p-button-plain max-w-13 h-13 hover:!bg-gray-200 dark:hover:!bg-zinc-800" />
        <Dialog v-model:visible="visible" modal :style="{ width: '25rem' }" :draggable="false"
            header="Share This Articles">


            <div class="my-4">
                <div class="flex items-center justify-around mb-6 gap-2">
                    <img :src="article.user.avatar ? article.user.avatar_url : `https://ui-avatars.com/api/?name=${getInitials(article.user.username)}&background=random`"
                        alt="" class="w-12 h-12 rounded-full object-cover border-2 border-green-500">
                    <h1
                        class="text-md font-bold text-gray-800 dark:text-gray-100 mb-4 md:text-lg items-center justify-center">
                        {{ article.title }}</h1>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Share this link via
                </p>

                <div class="flex justify-around mb-6 gap-2">
                    <!--  facebook  -->
                    <button @click="shareTo('facebook')"
                        class="flex items-center justify-center w-12 h-12 rounded-full text-gray-500 hover:text-white hover:bg-[#1877f2] dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M13.397 20.997v-8.196h2.765l.411-3.209h-3.176V7.548c0-.926.258-1.56 1.587-1.56h1.684V3.127A22.336 22.336 0 0 0 14.201 3c-2.444 0-4.122 1.492-4.122 4.231v2.355H7.332v3.209h2.753v8.202h3.312z">
                            </path>
                        </svg>
                    </button>
                    <!-- twitter (X) -->
                    <button @click="shareTo('twitter')"
                        class="flex items-center justify-center w-12 h-12 rounded-full text-gray-500 hover:text-black hover:bg-gray-200 dark:text-gray-400 dark:hover:text-black transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-twitter-x" viewBox="0 0 16 16">
                            <path
                                d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
                        </svg>
                    </button>
                    <!-- threads -->
                    <button @click="shareTo('threads')"
                        class="flex items-center justify-center w-12 h-12 rounded-full text-gray-500 hover:text-white hover:bg-black dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                            class="bi bi-threads" viewBox="0 0 16 16">
                            <path
                                d="M6.321 6.016c-.27-.18-1.166-.802-1.166-.802.756-1.081 1.753-1.502 3.132-1.502.975 0 1.803.327 2.394.948s.928 1.509 1.005 2.644q.492.207.905.484c1.109.745 1.719 1.86 1.719 3.137 0 2.716-2.226 5.075-6.256 5.075C4.594 16 1 13.987 1 7.994 1 2.034 4.482 0 8.044 0 9.69 0 13.55.243 15 5.036l-1.36.353C12.516 1.974 10.163 1.43 8.006 1.43c-3.565 0-5.582 2.171-5.582 6.79 0 4.143 2.254 6.343 5.63 6.343 2.777 0 4.847-1.443 4.847-3.556 0-1.438-1.208-2.127-1.27-2.127-.236 1.234-.868 3.31-3.644 3.31-1.618 0-3.013-1.118-3.013-2.582 0-2.09 1.984-2.847 3.55-2.847.586 0 1.294.04 1.663.114 0-.637-.54-1.728-1.9-1.728-1.25 0-1.566.405-1.967.868ZM8.716 8.19c-2.04 0-2.304.87-2.304 1.416 0 .878 1.043 1.168 1.6 1.168 1.02 0 2.067-.282 2.232-2.423a6.2 6.2 0 0 0-1.528-.161" />
                        </svg>
                    </button>
                    <!-- wa -->
                    <button @click="shareTo('whatsapp')"
                        class="flex items-center justify-center w-12 h-12 rounded-full text-gray-500 hover:text-white hover:bg-[#25D366] dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M18.403 5.633A8.919 8.919 0 0 0 12.053 3c-4.948 0-8.976 4.027-8.978 8.977 0 1.582.413 3.126 1.198 4.488L3 21.116l4.759-1.249a8.981 8.981 0 0 0 4.29 1.093h.004c4.947 0 8.975-4.027 8.977-8.977a8.926 8.926 0 0 0-2.627-6.35m-6.35 13.812h-.003a7.446 7.446 0 0 1-3.798-1.041l-.272-.162-2.824.741.753-2.753-.177-.282a7.448 7.448 0 0 1-1.141-3.971c.002-4.114 3.349-7.461 7.465-7.461a7.413 7.413 0 0 1 5.275 2.188 7.42 7.42 0 0 1 2.183 5.279c-.002 4.114-3.349 7.462-7.461 7.462m4.093-5.589c-.225-.113-1.327-.655-1.533-.73-.205-.075-.354-.112-.504.112s-.58.729-.711.879-.262.168-.486.056-.947-.349-1.804-1.113c-.667-.595-1.117-1.329-1.248-1.554s-.014-.346.099-.458c.101-.1.224-.262.336-.393.112-.131.149-.224.224-.374s.038-.281-.019-.393c-.056-.113-.505-1.217-.692-1.666-.181-.435-.366-.377-.504-.383a9.65 9.65 0 0 0-.429-.008.826.826 0 0 0-.599.28c-.206.225-.785.767-.785 1.871s.804 2.171.916 2.321c.112.15 1.582 2.415 3.832 3.387.536.231.954.369 1.279.473.537.171 1.026.146 1.413.089.431-.064 1.327-.542 1.514-1.066.187-.524.187-.973.131-1.067-.056-.094-.207-.151-.43-.263">
                            </path>
                        </svg>
                    </button>
                    <!-- tele -->
                    <button @click="shareTo('telegram')"
                        class="flex items-center justify-center w-12 h-12 rounded-full text-gray-500 hover:text-white hover:bg-[#229ED9] dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="m20.665 3.717-17.73 6.837c-1.21.486-1.203 1.161-.222 1.462l4.552 1.42 10.532-6.645c.498-.303.953-.14.579.192l-8.533 7.701h-.002l.002.001-.314 4.692c.46 0 .663-.211.921-.46l2.211-2.15 4.599 3.397c.848.467 1.457.227 1.668-.785l3.019-14.228c.309-1.239-.473-1.8-1.282-1.434z">
                            </path>
                        </svg>
                    </button>
                </div>

                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Or copy link</p>
                <div class="flex items-center rounded-lg border border-gray-300 dark:border-gray-700 p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="fill-gray-500 dark:fill-gray-400 mr-2">
                        <path
                            d="M8.465 11.293c1.133-1.133 3.109-1.133 4.242 0l.707.707 1.414-1.414-.707-.707c-.943-.944-2.199-1.465-3.535-1.465s-2.592.521-3.535 1.465L4.929 12a5.008 5.008 0 0 0 0 7.071 4.983 4.983 0 0 0 3.535 1.462A4.982 4.982 0 0 0 12 19.071l.707-.707-1.414-1.414-.707.707a3.007 3.007 0 0 1-4.243 0 3.005 3.005 0 0 1 0-4.243l2.122-2.121z">
                        </path>
                        <path
                            d="m12 4.929-.707.707 1.414 1.414.707-.707a3.007 3.007 0 0 1 4.243 0 3.005 3.005 0 0 1 0 4.243l-2.122 2.121c-1.133 1.133-3.109 1.133-4.242 0L10.586 12l-1.414 1.414.707.707c.943.944 2.199 1.465 3.535 1.465s2.592-.521 3.535-1.465L19.071 12a5.008 5.008 0 0 0 0-7.071 5.006 5.006 0 0 0-7.071 0z">
                        </path>
                    </svg>
                    <input class="w-full bg-transparent outline-none text-sm text-gray-800 dark:text-gray-200"
                        type="text" placeholder="Link" v-model="ArticleUrl" readonly />
                    <Toast />

                    <Button unstyled :icon="copyIcon" @click="show"
                        class="dark:hover:bg-zinc-800 text-gray-400 hover:text-gray-400 text-sm py-2 px-2 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center" />

                </div>
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Toast from 'primevue/toast';
import { computed, ref } from "vue";
import { useToast } from 'primevue/usetoast';
import { useClipboard } from "@vueuse/core";




const props = defineProps({
    article: Object,
});
const visible = ref(false);


const { text, isSupported, copy } = useClipboard()

const ArticleUrl = computed(() => {
    if (!props.article) return "";
    if (typeof route === "function") {
        return route("article.show", { id: props.article.id, slug: props.article.slug });
    }

    return `/article/${props.article.id}/${props.article.slug}`;
});

const toast = useToast();
const copyIcon = ref('pi pi-copy')
const show = () => {
    toast.add({ severity: 'secondary', summary: 'Copied', detail: 'Link Already Copied and ready to Share', life: 3000 });
    copyIcon.value = 'pi pi-check'
    copy(ArticleUrl.value)
};

// Share function
const stripHtml = (html) => {
    const tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
};

const shareTo = (platform) => {
    // Ensure title uses bolding for supported apps
    const title = `${props.article.title}`;
    // Clean HTML and get a short, readable description
    const desc = stripHtml(props.article.excerpt || props.article.content).substring(0, 150);
    const url = ArticleUrl.value;
    let shareUrl = '';

    // A clean, readable separator for the title and description
    const separator = "\n—\n";
    // An introductory phrase for chat apps
    const introText = "🔥 Check out this article! 🔥\n\n";

    switch (platform) {
        case 'facebook':
            // Facebook's sharer uses the OG tags from the URL, so we only pass the URL
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            break;
        case 'twitter':
            const twitterText = title + separator;
            shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(twitterText)}&url=${encodeURIComponent(url)}`;
            break;
        case 'whatsapp':
            // Prettier WhatsApp text: Intro + Title (bold) + Separator + Description + URL
            const whatsappText = introText + '*' + title + '*' + separator + desc + "\n\n" + url;
            shareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(whatsappText)}`;
            break;
        case 'telegram':
            // Prettier Telegram text: Title (bold) + Separator + Description
            const telegramText = title + separator + desc;
            shareUrl = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(telegramText)}`;
            break;
        case 'threads':
            // Prettier Threads text: Title (bold) + Separator + Description
            const threadsText = '"'+title+'"'  +  separator + desc + "\n\n" + url;
            shareUrl = `https://www.threads.net/intent/post?text=${encodeURIComponent(threadsText)}&url=${encodeURIComponent(url)}`;
            break;
        default:
            return;
    }

    window.open(shareUrl, '_blank', 'width=600,height=400');
};


</script>