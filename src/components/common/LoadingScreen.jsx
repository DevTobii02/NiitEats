import { useNavigate } from "react-router-dom";

export default function LoadingScreen() {
  const navigate = useNavigate();

  const handleStart = () => {
    navigate("/home");
  };

  return (
    <div
      className="fixed inset-0 flex flex-col items-center justify-center
      bg-cover bg-center z-50 animate-fadeIn"
      style={{ backgroundImage: "url('/loadingscreenhamburger.png')" }}
    >
      <img
        src="/loadingscreenhamburger.png"
        alt="Loading"
        className="w-32 h-32 mb-6 animate-bounce"
      />

      <h2 className="text-4xl font-extrabold text-white bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-transparent drop-shadow-lg mb-4 animate-slideUp">
        Welcome to FoodApp
      </h2>

      <button
        onClick={handleStart}
        className="px-8 py-4 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 mt-2 animate-fadeIn delay-500"
      >
        Get Started
      </button>
    </div>
  );
}
