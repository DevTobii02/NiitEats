import React from "react";
import Header from "../components/common/Header";
import Hero from "../components/home/Hero";
import CategoryGrid from "../components/home/CategoryGrid";
import FeaturedProducts from "../components/home/FeaturedProducts";
import Footer from "../components/common/Footer";
import IntroSplash from "../components/home/IntroSplash";

const Home = () => {
  return (
    <div className="min-h-screen flex flex-col bg-gray-50 text-gray-800">
      <IntroSplash />
      <Header />
      <main className="flex-grow">
        <Hero />
        <CategoryGrid />
        <FeaturedProducts />
      </main>
      <Footer />
    </div>
  );
};

export default Home;
