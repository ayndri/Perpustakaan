<?php

// Koleksi demo. ISBN, judul, penulis, dan penerbit nyata; sampul diambil dari Open Library
// berdasarkan ISBN dan sudah dicek satu per satu. Sinopsis ditulis ulang singkat.
// E-book hanya dipasang pada judul yang memang legal dibaca gratis.
//
// [isbn, judul, penulis, penerbit, tahun, kategori, eksemplar, lantai, nomor panggil, sinopsis, tautan e-book?, kuota?]
return [
    // Rekayasa Perangkat Lunak
    ['9780132350884', 'Clean Code', 'Robert C. Martin', 'Prentice Hall', 2008, 'Rekayasa Perangkat Lunak', 3, 2, '005.1 MAR', 'Panduan menulis kode yang mudah dibaca dan dirawat: penamaan, fungsi kecil, komentar yang perlu, dan cara merapikan kode lama sedikit demi sedikit.'],
    ['9780134494166', 'Clean Architecture', 'Robert C. Martin', 'Pearson', 2017, 'Rekayasa Perangkat Lunak', 2, 2, '005.1 MAR', 'Prinsip menyusun batas antarkomponen supaya aturan bisnis tidak bergantung pada framework, database, atau UI.'],
    ['9780137081073', 'The Clean Coder', 'Robert C. Martin', 'Prentice Hall', 2011, 'Rekayasa Perangkat Lunak', 1, 2, '005.1 MAR', 'Sikap profesional seorang programmer: berani berkata tidak, mengestimasi dengan jujur, dan menjaga ritme kerja.'],
    ['9780135957059', 'The Pragmatic Programmer', 'Andy Hunt, Dave Thomas', 'Addison-Wesley', 2019, 'Rekayasa Perangkat Lunak', 2, 2, '005.1 HUN', 'Kebiasaan kerja programmer yang matang, dari menjaga kode tetap fleksibel sampai bertanggung jawab atas karier sendiri. Edisi ulang tahun ke-20.'],
    ['9780134757599', 'Refactoring', 'Martin Fowler', 'Addison-Wesley', 2018, 'Rekayasa Perangkat Lunak', 1, 2, '005.16 FOW', 'Katalog teknik memperbaiki struktur kode tanpa mengubah perilakunya, edisi kedua dengan contoh JavaScript.'],
    ['9780201633610', 'Design Patterns', 'Erich Gamma dkk.', 'Addison-Wesley', 1995, 'Rekayasa Perangkat Lunak', 2, 2, '005.12 GAM', 'Buku "Gang of Four": 23 pola desain berorientasi objek beserta kapan dan mengapa memakainya.'],
    ['9781492078005', 'Head First Design Patterns', 'Eric Freeman, Elisabeth Robson', "O'Reilly", 2021, 'Rekayasa Perangkat Lunak', 2, 2, '005.12 FRE', 'Pengantar pola desain yang visual dan santai, cocok dibaca sebelum masuk ke buku Gang of Four.'],
    ['9780321125217', 'Domain-Driven Design', 'Eric Evans', 'Addison-Wesley', 2003, 'Rekayasa Perangkat Lunak', 1, 3, '005.1 EVA', 'Cara memodelkan perangkat lunak di sekitar bahasa dan aturan bisnis domainnya.'],
    ['9780735619678', 'Code Complete', 'Steve McConnell', 'Microsoft Press', 2004, 'Rekayasa Perangkat Lunak', 1, 3, '005.1 MCC', 'Referensi praktis konstruksi perangkat lunak: desain, penulisan, debugging, dan pengujian.'],
    ['9780201835953', 'The Mythical Man-Month', 'Frederick P. Brooks Jr.', 'Addison-Wesley', 1995, 'Rekayasa Perangkat Lunak', 1, 3, '005.1 BRO', 'Esai klasik manajemen proyek perangkat lunak, termasuk hukum Brooks: menambah orang ke proyek yang terlambat membuatnya makin terlambat.'],
    ['9781449373320', 'Designing Data-Intensive Applications', 'Martin Kleppmann', "O'Reilly", 2017, 'Rekayasa Perangkat Lunak', 2, 3, '005.74 KLE', 'Replikasi, partisi, transaksi, dan stream processing dijelaskan dari dasar. Bacaan wajib sebelum merancang sistem terdistribusi.'],
    ['9781491950357', 'Building Microservices', 'Sam Newman', "O'Reilly", 2015, 'Rekayasa Perangkat Lunak', 1, 3, '005.1 NEW', 'Kapan memecah sistem menjadi layanan kecil, cara membaginya, dan harga yang harus dibayar.'],
    ['9780134685991', 'Effective Java', 'Joshua Bloch', 'Addison-Wesley', 2017, 'Rekayasa Perangkat Lunak', 2, 3, '005.133 BLO', '90 aturan praktis menulis Java yang benar dan efisien, edisi ketiga untuk Java 9.'],
    ['9780596009205', 'Head First Java', 'Kathy Sierra, Bert Bates', "O'Reilly", 2005, 'Rekayasa Perangkat Lunak', 3, 3, '005.133 SIE', 'Belajar Java dan pemrograman berorientasi objek lewat gambar, teka-teki, dan latihan.'],
    ['9781492056355', 'Fluent Python', 'Luciano Ramalho', "O'Reilly", 2022, 'Rekayasa Perangkat Lunak', 1, 3, '005.133 RAM', 'Menulis Python yang idiomatis: model data, iterator, coroutine, dan metaprogramming. Edisi kedua.'],
    ['9781593279288', 'Python Crash Course', 'Eric Matthes', 'No Starch Press', 2019, 'Rekayasa Perangkat Lunak', 3, 3, '005.133 MAT', 'Pengantar Python berbasis proyek: game, visualisasi data, dan aplikasi web kecil.'],
    ['9781593279929', 'Automate the Boring Stuff with Python', 'Al Sweigart', 'No Starch Press', 2019, 'Rekayasa Perangkat Lunak', 2, 3, '005.133 SWE', 'Python untuk pekerjaan sehari-hari: mengolah spreadsheet, PDF, email, dan file. Penulisnya membuka versi daring gratis.', 'https://automatetheboringstuff.com/', 5],
    ['9781593279509', 'Eloquent JavaScript', 'Marijn Haverbeke', 'No Starch Press', 2018, 'Rekayasa Perangkat Lunak', 2, 3, '005.133 HAV', 'Pengantar pemrograman lewat JavaScript, dari dasar sampai Node.js. Versi daringnya gratis dari penulis.', 'https://eloquentjavascript.net/', 5],
    ['9780596517748', 'JavaScript: The Good Parts', 'Douglas Crockford', "O'Reilly", 2008, 'Rekayasa Perangkat Lunak', 1, 3, '005.133 CRO', 'Bagian JavaScript yang layak dipakai, dan bagian yang sebaiknya dihindari.'],

    // Algoritma & Ilmu Komputer
    ['9780262046305', 'Introduction to Algorithms', 'Thomas H. Cormen dkk.', 'MIT Press', 2022, 'Algoritma & Ilmu Komputer', 3, 1, '005.1 COR', 'Buku teks algoritma paling banyak dipakai di perkuliahan, edisi keempat.'],
    ['9780321573513', 'Algorithms', 'Robert Sedgewick, Kevin Wayne', 'Addison-Wesley', 2011, 'Algoritma & Ilmu Komputer', 2, 1, '005.1 SED', 'Algoritma dan struktur data dengan implementasi Java dan visualisasi yang jelas. Edisi keempat.'],
    ['9780262510875', 'Structure and Interpretation of Computer Programs', 'Harold Abelson, Gerald Jay Sussman', 'MIT Press', 1996, 'Algoritma & Ilmu Komputer', 1, 1, '005.13 ABE', 'Klasik MIT tentang abstraksi dan cara berpikir program. Teks lengkapnya dibuka gratis oleh MIT Press.', 'https://mitp-content-server.mit.edu/books/content/sectbyfn/books_pres_0/6515/sicp.zip/index.html', 3],
    ['9780131103627', 'The C Programming Language', 'Brian W. Kernighan, Dennis M. Ritchie', 'Prentice Hall', 1988, 'Algoritma & Ilmu Komputer', 2, 1, '005.133 KER', '"K&R": buku tipis yang mengajarkan C langsung dari perancangnya. Edisi kedua, ANSI C.'],
    ['9780984782857', 'Cracking the Coding Interview', 'Gayle Laakmann McDowell', 'CareerCup', 2015, 'Algoritma & Ilmu Komputer', 2, 1, '650.14 MCD', '189 soal wawancara pemrograman beserta pembahasannya.'],

    // Sistem, Jaringan & Basis Data
    ['9780134092669', "Computer Systems: A Programmer's Perspective", "Randal E. Bryant, David R. O'Hallaron", 'Pearson', 2015, 'Sistem, Jaringan & Basis Data', 2, 1, '004.2 BRY', 'Apa yang terjadi di bawah kode: representasi data, assembly, cache, memori virtual, dan konkurensi. Edisi ketiga.'],
    ['9781118063330', 'Operating System Concepts', 'Abraham Silberschatz dkk.', 'Wiley', 2013, 'Sistem, Jaringan & Basis Data', 3, 1, '005.43 SIL', '"Buku dinosaurus" sistem operasi: proses, penjadwalan, sinkronisasi, memori, dan sistem file. Edisi kesembilan.'],
    ['9780133594140', 'Computer Networking: A Top-Down Approach', 'James F. Kurose, Keith W. Ross', 'Pearson', 2016, 'Sistem, Jaringan & Basis Data', 3, 1, '004.6 KUR', 'Jaringan komputer dipelajari dari lapisan aplikasi turun ke lapisan fisik. Edisi ketujuh.'],
    ['9780073523323', 'Database System Concepts', 'Abraham Silberschatz dkk.', 'McGraw-Hill', 2010, 'Sistem, Jaringan & Basis Data', 2, 1, '005.74 SIL', 'Model relasional, SQL, normalisasi, indeks, dan transaksi. Edisi keenam.'],
    ['9781593272906', 'Practical Malware Analysis', 'Michael Sikorski, Andrew Honig', 'No Starch Press', 2012, 'Sistem, Jaringan & Basis Data', 1, 1, '005.84 SIK', 'Membedah malware di lab yang aman: analisis statis, dinamis, dan reverse engineering dengan IDA dan debugger.'],

    // Kecerdasan Buatan
    ['9780136042594', 'Artificial Intelligence: A Modern Approach', 'Stuart Russell, Peter Norvig', 'Pearson', 2009, 'Kecerdasan Buatan', 2, 2, '006.3 RUS', 'Buku teks AI paling banyak dipakai: pencarian, logika, probabilitas, dan pembelajaran mesin. Edisi ketiga.'],
    ['9780262035613', 'Deep Learning', 'Ian Goodfellow, Yoshua Bengio, Aaron Courville', 'MIT Press', 2016, 'Kecerdasan Buatan', 1, 2, '006.31 GOO', 'Dasar matematis jaringan saraf dalam. Teks lengkapnya bisa dibaca gratis di situs resmi buku ini.', 'https://www.deeplearningbook.org/', 4],
    ['9781617294433', 'Deep Learning with Python', 'François Chollet', 'Manning', 2017, 'Kecerdasan Buatan', 2, 2, '006.31 CHO', 'Deep learning praktis dengan Keras, ditulis oleh pembuat Keras sendiri.'],
    ['9781098125974', 'Hands-On Machine Learning with Scikit-Learn, Keras & TensorFlow', 'Aurélien Géron', "O'Reilly", 2022, 'Kecerdasan Buatan', 2, 2, '006.31 GER', 'Machine learning dan deep learning lewat contoh kode Python, edisi ketiga.'],

    // Desain & UX
    ['9780465050659', 'The Design of Everyday Things', 'Don Norman', 'Basic Books', 2013, 'Desain & UX', 2, 2, '745.2 NOR', 'Kenapa pintu yang membingungkan adalah salah perancangnya, bukan penggunanya. Edisi revisi.'],
    ['9780321965516', "Don't Make Me Think, Revisited", 'Steve Krug', 'New Riders', 2014, 'Desain & UX', 2, 2, '006.7 KRU', 'Usability web dan mobile dalam bahasa sehari-hari, termasuk cara menguji dengan anggaran nol.'],

    // Bisnis & Startup
    ['9780307887894', 'The Lean Startup', 'Eric Ries', 'Crown Business', 2011, 'Bisnis & Startup', 2, 2, '658.11 RIE', 'Bangun, ukur, belajar: menguji ide bisnis dengan produk minimum sebelum menghabiskan modal.'],
    ['9780804139298', 'Zero to One', 'Peter Thiel, Blake Masters', 'Crown Business', 2014, 'Bisnis & Startup', 1, 2, '658.11 THI', 'Catatan kuliah Thiel tentang membangun perusahaan yang menciptakan sesuatu yang benar-benar baru.'],
    ['9780062273208', 'The Hard Thing About Hard Things', 'Ben Horowitz', 'HarperBusiness', 2014, 'Bisnis & Startup', 1, 2, '658.4 HOR', 'Memimpin perusahaan ketika tidak ada jawaban mudah: PHK, pivot, dan krisis keuangan.'],

    // Sains Populer
    ['9780553380163', 'A Brief History of Time', 'Stephen Hawking', 'Bantam', 1998, 'Sains Populer', 2, 4, '523.1 HAW', 'Asal-usul alam semesta, lubang hitam, dan arah waktu, untuk pembaca tanpa latar fisika.'],
    ['9780393609394', 'Astrophysics for People in a Hurry', 'Neil deGrasse Tyson', 'W. W. Norton', 2017, 'Sains Populer', 2, 4, '523.01 TYS', 'Kosmos dalam bab-bab pendek yang bisa selesai dibaca sambil menunggu kelas mulai.'],
    ['9780062316097', 'Sapiens', 'Yuval Noah Harari', 'Harper', 2015, 'Sains Populer', 2, 4, '909 HAR', 'Sejarah singkat umat manusia, dari revolusi kognitif sampai revolusi sains.'],

    // Pengembangan Diri
    ['9780735211292', 'Atomic Habits', 'James Clear', 'Avery', 2018, 'Pengembangan Diri', 1, 2, '158.1 CLE', 'Membangun kebiasaan baik lewat perubahan kecil yang konsisten.'],
    ['9781455586691', 'Deep Work', 'Cal Newport', 'Grand Central', 2016, 'Pengembangan Diri', 2, 2, '650.1 NEW', 'Kemampuan fokus tanpa gangguan sebagai keunggulan langka, dan cara melatihnya.'],
    ['9780374533557', 'Thinking, Fast and Slow', 'Daniel Kahneman', 'Farrar, Straus and Giroux', 2013, 'Pengembangan Diri', 2, 2, '153.4 KAH', 'Dua sistem berpikir manusia dan bias-bias yang lahir dari keduanya, oleh peraih Nobel Ekonomi.'],
    ['9780062457714', 'The Subtle Art of Not Giving a F*ck', 'Mark Manson', 'HarperOne', 2016, 'Pengembangan Diri', 2, 2, '158.1 MAN', 'Memilih dengan sadar hal yang layak dipedulikan, alih-alih berusaha bahagia setiap saat.'],

    // Sastra Indonesia
    ['9789799731234', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 2005, 'Sastra Indonesia', 3, 4, '899.221 TOE', 'Kisah Minke di akhir abad ke-19, buku pertama Tetralogi Buru.'],
    ['9786024246945', 'Laut Bercerita', 'Leila S. Chudori', 'Kepustakaan Populer Gramedia', 2017, 'Sastra Indonesia', 0, 4, '899.221 CHU', 'Novel tentang aktivis mahasiswa yang dihilangkan menjelang 1998, dan keluarga yang menunggu mereka pulang.'],
    ['9789799105158', 'Pulang', 'Leila S. Chudori', 'Kepustakaan Populer Gramedia', 2012, 'Sastra Indonesia', 2, 4, '899.221 CHU', 'Para eksil politik 1965 di Paris yang tidak bisa pulang, dan anak yang mencari jejak ayahnya di Jakarta 1998.'],
    ['9786020312583', 'Cantik Itu Luka', 'Eka Kurniawan', 'Gramedia Pustaka Utama', 2015, 'Sastra Indonesia', 2, 4, '899.221 KUR', 'Kisah Dewi Ayu dan keturunannya, dari masa kolonial sampai sesudah 1965, ditulis dengan realisme magis.'],
    ['9789792248616', 'Negeri 5 Menara', 'A. Fuadi', 'Gramedia Pustaka Utama', 2009, 'Sastra Indonesia', 2, 4, '899.221 FUA', 'Enam santri di Pondok Madani dan mantra "man jadda wajada".'],
    ['9786020324784', 'Hujan', 'Tere Liye', 'Gramedia Pustaka Utama', 2016, 'Sastra Indonesia', 2, 4, '899.221 LIY', 'Kisah cinta dan kehilangan di masa depan setelah bencana besar.'],
    ['9786028997904', 'Rindu', 'Tere Liye', 'Republika', 2014, 'Sastra Indonesia', 2, 4, '899.221 LIY', 'Perjalanan kapal haji tahun 1938 dari Makassar, dengan pertanyaan-pertanyaan yang dibawa para penumpangnya.'],

    // Sastra Dunia
    ['9780199535569', 'Pride and Prejudice', 'Jane Austen', 'Oxford University Press', 2008, 'Sastra Dunia', 1, 4, '823.7 AUS', 'Elizabeth Bennet dan Mr. Darcy. Teks aslinya sudah domain publik di Project Gutenberg.', 'https://www.gutenberg.org/ebooks/1342', 5],
    ['9780743273565', 'The Great Gatsby', 'F. Scott Fitzgerald', 'Scribner', 2004, 'Sastra Dunia', 2, 4, '813.52 FIT', 'Pesta-pesta Gatsby dan mimpi Amerika yang retak di tahun 1920-an. Teksnya sudah domain publik di Project Gutenberg.', 'https://www.gutenberg.org/ebooks/64317', 5],
    ['9780451524935', '1984', 'George Orwell', 'Signet Classics', 1961, 'Sastra Dunia', 2, 4, '823.912 ORW', 'Winston Smith di bawah pengawasan Bung Besar, novel yang melahirkan istilah "orwellian".'],
    ['9780061120084', 'To Kill a Mockingbird', 'Harper Lee', 'Harper Perennial', 2006, 'Sastra Dunia', 2, 4, '813.54 LEE', 'Scout Finch menyaksikan ayahnya membela seorang kulit hitam di pengadilan Alabama tahun 1930-an.'],
    ['9780316769488', 'The Catcher in the Rye', 'J. D. Salinger', 'Little, Brown', 1991, 'Sastra Dunia', 1, 4, '813.54 SAL', 'Tiga hari Holden Caulfield berkeliaran di New York setelah dikeluarkan dari sekolah.'],
    ['9780062315007', 'The Alchemist', 'Paulo Coelho', 'HarperOne', 2014, 'Sastra Dunia', 2, 4, '869.3 COE', 'Santiago, gembala Andalusia, menyeberangi gurun untuk mencari harta yang ia mimpikan.'],
    ['9780547928227', 'The Hobbit', 'J. R. R. Tolkien', 'Houghton Mifflin Harcourt', 2012, 'Sastra Dunia', 2, 4, '823.912 TOL', 'Bilbo Baggins diseret dalam perjalanan merebut kembali harta kurcaci dari naga Smaug.'],
    ['9780590353427', "Harry Potter and the Sorcerer's Stone", 'J. K. Rowling', 'Scholastic', 1998, 'Sastra Dunia', 2, 4, '823.914 ROW', 'Tahun pertama Harry di Hogwarts.'],
    ['9780441172719', 'Dune', 'Frank Herbert', 'Ace', 1990, 'Sastra Dunia', 1, 4, '813.54 HER', 'Paul Atreides di planet gurun Arrakis, satu-satunya sumber rempah paling berharga di alam semesta.'],
    ['9780439023481', 'The Hunger Games', 'Suzanne Collins', 'Scholastic', 2008, 'Sastra Dunia', 2, 4, '813.6 COL', 'Katniss menggantikan adiknya dalam pertarungan hidup-mati yang disiarkan ke seluruh Panem.'],
    ['9780385737951', 'The Maze Runner', 'James Dashner', 'Delacorte Press', 2009, 'Sastra Dunia', 1, 4, '813.6 DAS', 'Thomas terbangun di tengah labirin raksasa tanpa ingatan apa pun selain namanya.'],
];
