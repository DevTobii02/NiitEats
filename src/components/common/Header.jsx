import { Link } from "react-router-dom";

export default function Header() {
  return (
    <header className="bg-orange-500 text-white shadow-md sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        
        {/* Logo + Site Name */}
        <Link to="/" className="flex items-center space-x-2">
          <img
            src="/logo.png"
            alt="TastyBites"
            className="w-10 h-10 object-cover rounded-full"
          />
          <span className="text-2xl font-bold">TastyBites</span>
        </Link>

        {/* Navigation */}
        <nav className="space-x-4 hidden md:flex">
          <Link to="/" className="hover:underline">
            Home
          </Link>
          <Link to="/products" className="hover:underline">
            Products
          </Link>
          <Link to="/cart" className="hover:underline">
            Cart
          </Link>
          <Link to="/profile" className="hover:underline">
            Profile
          </Link>
          <Link to="/login" className="hover:underline">
            Login
          </Link>
        </nav>

        {/* Mobile Hamburger Placeholder */}
        <div className="md:hidden">
          {/* Later you can add a hamburger menu here */}
          <button className="focus:outline-none">☰</button>
        </div>
      </div>
    </header>
  );
}
