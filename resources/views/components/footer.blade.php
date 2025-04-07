<footer class="flex items-end justify-center" id="footer">
    <div class="w-full">
        <div class="px-1 py-16">
            <div class="container mx-auto">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-4 xl:grid-cols-6">
                    <div class="md:col-span-2">
                        <a class="navbar-brand mb-6 block" href="{{ route('article.index') }}">
                            <h2 class="text-primary dark:text-dark-primary text-3xl font-bold">Zakialawi Blog</h2>
                        </a>
                        <p class="text-muted dark:text-dark-muted max-w-xs text-base font-medium">Personal Blog & platform</p>

                        <h3 class="text-dark dark:text-dark-light mt-5 text-xl font-bold">Follow Us:</h3>
                        <div class="text-dark dark:text-dark-light mt-4 flex gap-3 font-normal">
                            <a class="hover:border-primary hover:bg-primary hover:text-light border-neutral dark:border-dark-muted flex h-10 w-10 items-center justify-center rounded-md border bg-transparent text-xl transition-all duration-500" href="#" target="_blank">
                                <i class="ri-facebook-fill"></i>
                            </a>
                            <a class="hover:border-primary hover:bg-primary hover:text-light border-neutral dark:border-dark-muted flex h-10 w-10 items-center justify-center rounded-md border bg-transparent text-xl transition-all duration-500" href="https://twitter.com/zakialawi_" target="_blank">
                                <i class="ri-twitter-x-fill"></i>
                            </a>
                            <a class="hover:border-primary hover:bg-primary hover:text-light border-neutral dark:border-dark-muted flex h-10 w-10 items-center justify-center rounded-md border bg-transparent text-xl transition-all duration-500" href="https://www.linkedin.com/in/ahmad-zaki-alawi/" target="_blank">
                                <i class="ri-linkedin-box-fill"></i>
                            </a>
                            <a class="hover:border-primary hover:bg-primary hover:text-light border-neutral dark:border-dark-muted flex h-10 w-10 items-center justify-center rounded-md border bg-transparent text-xl transition-all duration-500" href="https://www.instagram.com/zakialawi_/" target="_blank">
                                <i class="ri-instagram-fill"></i>
                            </a>
                        </div>
                    </div>

                    <div class="flex flex-col gap-5">
                        <h5 class="text-dark dark:text-dark-light text-2xl font-bold">About</h5>
                        <div class="text-dark dark:text-dark-light space-y-1">
                            <div>
                                <a class="hover:text-primary dark:hover:text-dark-primary text-lg transition-all duration-300" href="#">Menu 1</a>
                            </div>
                            <div>
                                <a class="hover:text-primary dark:hover:text-dark-primary text-lg transition-all duration-300" href="#">Menu 1</a>
                            </div>
                            <div>
                                <a class="hover:text-primary dark:hover:text-dark-primary text-lg transition-all duration-300" href="#">Menu 1</a>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-5">
                        <h5 class="text-dark dark:text-dark-light text-2xl font-bold">Blog</h5>
                        <div class="text-dark dark:text-dark-light space-y-1">
                            <div>
                                <a class="hover:text-primary dark:hover:text-dark-primary text-lg transition-all duration-300" href="#">Blog 1</a>
                            </div>
                            <div>
                                <a class="hover:text-primary dark:hover:text-dark-primary text-lg transition-all duration-300" href="#">Blog 1</a>
                            </div>
                            <div>
                                <a class="hover:text-primary dark:hover:text-dark-primary text-lg transition-all duration-300" href="#">Blog 1</a>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="flex flex-col">
                            <h5 class="text-dark dark:text-dark-light mb-6 text-2xl font-bold">Contact Us</h5>
                            <p class="text-muted dark:text-dark-muted mt-2 text-base font-medium">hallo@zakialawi.my.id</p>
                            <form class="ms-auto mt-6 w-full max-w-lg">
                                <div class="dark:bg-dark-base-200 relative flex items-center overflow-hidden rounded-md bg-white px-1 shadow">
                                    <input class="dark:text-dark-light w-full border-0 bg-white px-3 py-3.5 text-base text-black outline-none ring-0 focus:ring-0 dark:bg-transparent" name="email" type="email" placeholder="Your Email Address">
                                    <button class="bg-secondary dark:bg-dark-primary hover:bg-primary dark:hover:bg-dark-secondary rounded px-3 py-1 font-semibold text-white transition-all duration-500" id="send-email-button" type="button">
                                        <i class="ri-send-plane-2-line"></i>
                                    </button>
                                </div>
                            </form>
                            <div id="message-newsletter"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="dark:border-dark-muted border-t border-gray-300 p-6">
            <div class="container">
                <div class="flex flex-wrap items-center justify-center gap-6 sm:justify-between">
                    <p class="text-muted dark:text-dark-muted text-base font-semibold">
                        Copyright &copy;
                        <script>
                            document.write(new Date().getFullYear())
                        </script>. All rights reserved.
                    </p>

                    <div>
                        <a class="hover:text-primary dark:hover:text-dark-primary text-muted dark:text-dark-muted text-base font-semibold" href="{{ route('termsAndConditions') }}">Terms Conditions</a>
                        <span class="text-muted dark:text-dark-muted text-base font-semibold"> &amp;</span>
                        <a class="hover:text-primary dark:hover:text-dark-primary text-muted dark:text-dark-muted text-base font-semibold" href="{{ route('privacyPolicy') }}">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</footer>

@push('javascript')
    <script>
        $(document).ready(function() {
            $("#send-email-button").click(function(e) {
                $.ajax({
                    type: "post",
                    url: "{{ route('newsletter.store') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "email": $("input[name=email]").val(),
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $("#message-newsletter").html(`<div class="text-info" role="alert">Sending...</div>`);
                    },
                    success: function(response) {
                        const html = `<div class="text-success" role="alert">${response.message}</div>`;
                        $("#message-newsletter").html(html);
                        if (response.message.success == true) {
                            $("input[name=email]").val("");
                        }
                    },
                    error: function(error) {
                        const html = `<div class="text-error" role="alert">${error.responseJSON.message}</div>`;
                        $("#message-newsletter").html(html);
                    },
                });
            });
        });
    </script>
@endpush
