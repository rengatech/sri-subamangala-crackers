<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

defineProps({ Gallery: Object });
</script>

<template>
    <AppLayout>
        <div style="display: flex; position: sticky; top: 0; z-index: 100000">
            <Link href="https://wa.link/zt2tjo">
                <img src="assets/img/whatsapp.png" class="whatsapp"
            /></Link>
        </div>
        <div style="display: flex; position: sticky; top: 0; z-index: 100000">
            <!-- <a
                href="assets/img/Pandas_pricelist_2021.pdf"
                download="pricelist"
            >
                <img
                    src="assets/img/prcelist.png"
                    class="booknow blink"
                />Booknow</a
            > -->
        </div>
        <!-- ======= Portfolio Section ======= -->
        <section id="portfolio" class="portfolio">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2>Product List</h2>
                    <p>Our Products</p>
                </div>

                <div class="row" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-lg-12 d-flex justify-content-center">
                        <ul id="portfolio-flters">
                            <li data-filter="*" class="filter-active">All</li>
                            <li data-filter=".filter-app">Flowerpot</li>
                            <li data-filter=".filter-card">Ground Chakkar</li>
                            <li data-filter=".filter-web">Fancy</li>
                        </ul>
                    </div>
                </div>

                <div
                    class="row portfolio-container"
                    data-aos="fade-up"
                    data-aos-delay="200"
                >
                    <div class="col-lg-4 col-md-6 portfolio-item filter-app" v-for="galleryimg in Gallery" :key="galleryimg.id"
                    >
                        <div class="portfolio-wrap">
                            <img
                                src="assets/img/portfolio/portfolio-1.jpg"
                                class="img-fluid"
                                alt=""
                            />
                            <div class="portfolio-info">
                                <h4>Flowerpot</h4>
                                <p>App</p>
                                <div class="portfolio-links">
                                    <Link
                                        href="assets/img/portfolio/portfolio-1.jpg"
                                        data-gallery="portfolioGallery"
                                        class="portfolio-lightbox"
                                        title="App 1"
                                        ><i class="bx bx-plus"></i
                                    ></Link>
                                    <Link href="/portfolio" title="More Details"
                                        ><i class="bx bx-link"></i
                                    ></Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 portfolio-item filter-web">
                        <div class="portfolio-wrap">
                            <img
                                src="assets/img/portfolio/portfolio-2.jpg"
                                class="img-fluid"
                                alt=""
                            />
                            <div class="portfolio-info">
                                <h4>Ground Chakkar</h4>
                                <p>Web</p>
                                <div class="portfolio-links">
                                    <Link
                                        href="assets/img/portfolio/portfolio-2.jpg"
                                        data-gallery="portfolioGallery"
                                        class="portfolio-lightbox"
                                        title="Web 3"
                                        ><i class="bx bx-plus"></i
                                    ></Link>
                                    <Link href="/portfolio" title="More Details"
                                        ><i class="bx bx-link"></i
                                    ></Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 portfolio-item filter-app">
                        <div class="portfolio-wrap">
                            <img
                                src="assets/img/portfolio/portfolio-3.jpg"
                                class="img-fluid"
                                alt=""
                            />
                            <div class="portfolio-info">
                                <h4>Wala</h4>
                                <p>App</p>
                                <div class="portfolio-links">
                                    <Link
                                        href="assets/img/portfolio/portfolio-3.jpg"
                                        data-gallery="portfolioGallery"
                                        class="portfolio-lightbox"
                                        title="App 2"
                                        ><i class="bx bx-plus"></i
                                    ></Link>
                                    <Link href="/portfolio" title="More Details"
                                        ><i class="bx bx-link"></i
                                    ></Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 portfolio-item filter-card">
                        <div class="portfolio-wrap">
                            <img
                                src="assets/img/portfolio/portfolio-4.jpg"
                                class="img-fluid"
                                alt=""
                            />
                            <div class="portfolio-info">
                                <h4>Siren</h4>
                                <p>Card</p>
                                <div class="portfolio-links">
                                    <Link
                                        href="assets/img/portfolio/portfolio-4.jpg"
                                        data-gallery="portfolioGallery"
                                        class="portfolio-lightbox"
                                        title="Card 2"
                                        ><i class="bx bx-plus"></i
                                    ></Link>
                                    <Link href="/portfolio" title="More Details"
                                        ><i class="bx bx-link"></i
                                    ></Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 portfolio-item filter-web">
                        <div class="portfolio-wrap">
                            <img
                                src="assets/img/portfolio/portfolio-5.jpg"
                                class="img-fluid"
                                alt=""
                            />
                            <div class="portfolio-info">
                                <h4>r</h4>
                                <p>Web</p>
                                <div class="portfolio-links">
                                    <Link
                                        href="assets/img/portfolio/portfolio-5.jpg"
                                        data-gallery="portfolioGallery"
                                        class="portfolio-lightbox"
                                        title="Web 2"
                                        ><i class="bx bx-plus"></i
                                    ></Link>
                                    <Link href="/portfolio" title="More Details"
                                        ><i class="bx bx-link"></i
                                    ></Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 portfolio-item filter-app">
                        <div class="portfolio-wrap">
                            <img
                                src="assets/img/portfolio/portfolio-6.jpg"
                                class="img-fluid"
                                alt=""
                            />
                            <div class="portfolio-info">
                                <h4>App 3</h4>
                                <p>App</p>
                                <div class="portfolio-links">
                                    <Link
                                        href="assets/img/portfolio/portfolio-6.jpg"
                                        data-gallery="portfolioGallery"
                                        class="portfolio-lightbox"
                                        title="App 3"
                                        ><i class="bx bx-plus"></i
                                    ></Link>
                                    <Link href="/portfolio" title="More Details"
                                        ><i class="bx bx-link"></i
                                    ></Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 portfolio-item filter-card">
                        <div class="portfolio-wrap">
                            <img
                                src="assets/img/portfolio/portfolio-1.jpg"
                                class="img-fluid"
                                alt=""
                            />
                            <div class="portfolio-info">
                                <h4>Card 1</h4>
                                <p>Card</p>
                                <div class="portfolio-links">
                                    <Link
                                        href="assets/img/portfolio/portfolio-1.jpg"
                                        data-gallery="portfolioGallery"
                                        class="portfolio-lightbox"
                                        title="Card 1"
                                        ><i class="bx bx-plus"></i
                                    ></Link>
                                    <Link href="/portfolio" title="More Details"
                                        ><i class="bx bx-link"></i
                                    ></Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 portfolio-item filter-card">
                        <div class="portfolio-wrap">
                            <img
                                src="assets/img/portfolio/portfolio-2.jpg"
                                class="img-fluid"
                                alt=""
                            />
                            <div class="portfolio-info">
                                <h4>Card 3</h4>
                                <p>Card</p>
                                <div class="portfolio-links">
                                    <Link
                                        href="assets/img/portfolio/portfolio-2.jpg"
                                        data-gallery="portfolioGallery"
                                        class="portfolio-lightbox"
                                        title="Card 3"
                                        ><i class="bx bx-plus"></i
                                    ></Link>
                                    <Link href="/portfolio" title="More Details"
                                        ><i class="bx bx-link"></i
                                    ></Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 portfolio-item filter-web">
                        <div class="portfolio-wrap">
                            <img
                                src="assets/img/portfolio/portfolio-5.jpg"
                                class="img-fluid"
                                alt=""
                            />
                            <div class="portfolio-info">
                                <h4>Web 3</h4>
                                <p>Web</p>
                                <div class="portfolio-links">
                                    <Link
                                        href="assets/img/portfolio/portfolio-5.jpg"
                                        data-gallery="portfolioGallery"
                                        class="portfolio-lightbox"
                                        title="Web 3"
                                        ><i class="bx bx-plus"></i
                                    ></Link>
                                    <Link href="/portfolio" title="More Details"
                                        ><i class="bx bx-link"></i
                                    ></Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Portfolio Section -->
    </AppLayout>
</template>
