        <!-- TODO ini pisahin jadi component -->
        <ul class="grid grid-cols-4 grid-rows-2 gap-y-10 gap-x-6 items-start pt-8 pb-8 mx-auto relative max-w-[1200px]">
            <li class="relative flex flex-col sm:flex-row items-start col-span-full" v-for="category in categorized">
                <a :href="route('article.show', category.articles[0].slug)"
                    class="flex flex-col sm:flex-row items-start gap-4 w-full no-underline">
                    <div class="border-b-2 flex-1">
                        <h3 class="mb-1 text-slate-900 dark:text-gray-300 font-semibold">
                            <span class="mb-1 block text-sm leading-6 text-indigo-500 relative font-light">
                                <a :href="route('profile.show', category.articles[0].user.username)">
                                    <img class="rounded-full size-6 inline-block mr-2"
                                        :src="category.articles[0].user.avatar ? `/storage/${category.articles[0].user.avatar}` : 'https://ui-avatars.com/api/?name=' + category.articles[0].user.username"
                                        alt="">
                                </a>
                                <a :href="route('profile.show', category.articles[0].user.username)"
                                    class="relative inline-block group transition duration-300">
                                    {{ category.articles[0].user.username }}
                                    <span
                                        class="absolute left-0 -bottom-0.5 w-0 group-hover:w-full h-0.5 bg-sky-600 transition-all duration-500"></span>
                                </a>
                                in
                                <a :href="route('category.show', category.slug)"
                                    class="relative inline-block group transition duration-300">
                                    {{ category.name }}
                                    <span
                                        class="absolute left-0 -bottom-0.5 w-0 group-hover:w-full h-0.5 bg-indigo-500 transition-all duration-500"></span>
                                </a>
                            </span>
                            {{ category.articles[0].title }}
                        </h3>

                        <div class="prose prose-slate prose-sm text-slate-600 dark:text-gray-400"
                            v-html="category.articles[0].excerpt">
                        </div>

                        <div class="my-6 flex items-center gap-10 justify-between">
                            <time :datetime="category.articles[0].created_at"
                                class="font-mono text-[12px] text-gray-400">
                                {{ dayjs(category.articles[0].created_at).format('MMMM DD') }}
                            </time>
                            <span class="flex items-center text-[12px] !text-gray-400 font-mono gap-4"
                                :style="{ color: liked ? 'red' : 'gray' }">
                                <span class="flex items-center">
                                    <i class="pi pi-comment mr-1"></i> {{ CommentCount ? CommentCount : '123' }}
                                </span>
                                <span class="flex items-center">
                                    <i :class="liked ? 'pi pi-heart-fill mr-1' : 'pi pi-heart mr-1'"></i> {{ LikeCount ?
                                        LikeCount : '123' }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <img :src="category.articles[0].cover ? `${category.articles[0].cover}` : ``" alt=""
                        class="shadow-md rounded-lg bg-slate-50 w-[17rem] mb-0 flex-shrink-0" width="1216" height="640">
                </a>
            </li>
        </ul>