# Tutorshub

/* General Body Styles */
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    line-height: 1.6;
    color: #333;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px 0;
}

h1, h2, h3, h4 {
    color: #0056b3;
    margin-bottom: 15px;
}

p {
    margin-bottom: 15px;
}

a {
    text-decoration: none;
    color: #007bff;
}

a:hover {
    color: #0056b3;
}

.btn {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    padding: 12px 25px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
    margin-top: 20px;
}

.btn:hover {
    background-color: #0056b3;
}

/* Header Styles */
header {
    background-color: #f8f9fa;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header .logo h1 {
    margin: 0;
    color: #007bff;
    font-size: 2em;
}

header nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
}

header nav ul li {
    margin-left: 25px;
}

header nav ul li a {
    color: #333;
    font-weight: 600;
    transition: color 0.3s ease;
}

header nav ul li a:hover {
    color: #007bff;
}

/* Hero Section */
#hero {
    background: linear-gradient to right, #e0f2f7, #c1e7f0); /* Light blue gradient */
    color: #0056b3;
    text-align: center;
    padding: 100px 0;
}

#hero h2 {
    font-size: 3.5em;
    margin-bottom: 20px;
    color: #0056b3;
}

#hero p {
    font-size: 1.3em;
    margin-bottom: 30px;
    color: #333;
}

/* Section Styling (General) */
section {
    padding: 60px 0;
}

section:nth-of-type(odd) {
    background-color: #f9f9f9;
}

.about-section, .features-section, .contact-section {
    text-align: center;
}

/* Course Grid and Feature Grid */
.course-grid, .feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.course-card, .feature-item {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 30px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.course-card:hover, .feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.course-card h4, .feature-item h4 {
    font-size: 1.5em;
    margin-bottom: 10px;
    color: #007bff;
}

.course-card p, .feature-item p {
    font-size: 1em;
    color: #555;
}

/* Contact Section */
.contact-section .btn {
    margin-bottom: 15px;
}

/* Footer */
footer {
    background-color: #333;
    color: #fff;
    text-align: center;
    padding: 25px 0;
    margin-top: 40px;
}

footer p {
    margin: 0;
    font-size: 0.9em;
}

/* Responsive Design */
@media (max-width: 768px) {
    header .container {
        flex-direction: column;
        text-align: center;
    }

    header nav ul {
        margin-top: 15px;
        flex-direction: column;
        align-items: center;
    }

    header nav ul li {
        margin: 10px 0;
    }

    #hero h2 {
        font-size: 2.5em;
    }

    #hero p {
        font-size: 1.1em;
    }

    .course-grid, .feature-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    #hero {
        padding: 60px 0;
    }

    #hero h2 {
        font-size: 2em;
    }

    .btn {
        padding: 10px 20px;
        font-size: 0.9em;
    }

    .course-card, .feature-item {
        padding: 20px;
    }

    .course-card h4, .feature-item h4 {
        font-size: 1.3em;
    }
}
