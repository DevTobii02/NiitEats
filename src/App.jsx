import "./App.css";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";

// Pages
import Home from "./pages/Home";
import Products from "./pages/Products";
import ProductDetail from "./pages/ProductDetail";
import Cart from "./pages/Cart";
import Checkout from "./pages/Checkout";
import Profile from "./pages/Profile";

// Auth
import LoginForm from "./components/auth/LoginForm";
import RegisterForm from "./components/auth/RegisterForm";

// Layout
import Header from "./components/common/Header";
import Footer from "./components/common/Footer";

// Loading screen
import LoadingScreen from "./components/common/LoadingScreen";

function App() {
  return (
    <Router>
      <Routes>
        {/* Loading screen FIRST, no header/footer */}
        <Route path="/" element={<LoadingScreen />} />

        {/* Normal pages with header/footer */}
        <Route
          path="/home"
          element={
            <>
              <Header />
              <Home />
              <Footer />
            </>
          }
        />

        <Route
          path="/products"
          element={
            <>
              <Header />
              <Products />
              <Footer />
            </>
          }
        />

        <Route
          path="/product/:id"
          element={
            <>
              <Header />
              <ProductDetail />
              <Footer />
            </>
          }
        />

        <Route
          path="/cart"
          element={
            <>
              <Header />
              <Cart />
              <Footer />
            </>
          }
        />

        <Route
          path="/checkout"
          element={
            <>
              <Header />
              <Checkout />
              <Footer />
            </>
          }
        />

        <Route
          path="/profile"
          element={
            <>
              <Header />
              <Profile />
              <Footer />
            </>
          }
        />

        <Route path="/login" element={<LoginForm />} />
        <Route path="/register" element={<RegisterForm />} />

        {/* fallback */}
        <Route path="*" element={<Home />} />
      </Routes>
    </Router>
  );
}

export default App;
