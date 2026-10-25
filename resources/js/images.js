// Import critical images so vite-imagetools generates WebP/AVIF + srcset
// Hero background
import heroBg from '../../public/images/hero-bg.jpg?w=800,1200,1600&format=webp,avif&as=picture';

// Page header background (used on inner pages)
import pageHeaderBg from '../../public/images/page-header-bg.jpg?w=800,1200,1600&format=webp,avif&as=picture';

// Why choose us item images (fallback used on homepage)
import whyItem1 from '../../public/images/why-choose-item-img-1.jpg?w=400,800,1200&format=webp,avif&as=picture';
import whyItem2 from '../../public/images/why-choose-item-img-2.jpg?w=400,800,1200&format=webp,avif&as=picture';

// Dark section background shape
import darkSectionShape from '../../public/images/dark-section-bg-shape.png?format=webp,avif&as=picture';

// Core feature background image
import coreFeatureBg from '../../public/images/core-feature-image-bg.png?format=webp,avif&as=picture';

// Testimonial background
import testimonialBg from '../../public/images/testimonial-bg.jpg?w=800,1200&format=webp,avif&as=picture';

// Footer background
import footerBg from '../../public/images/footer-bg.jpg?w=800,1200&format=webp,avif&as=picture';

// Export an object for possible runtime use (e.g., inline critical CSS)
export const criticalImages = {
    heroBg,
    pageHeaderBg,
    whyItem1,
    whyItem2,
    darkSectionShape,
    coreFeatureBg,
    testimonialBg,
    footerBg,
};