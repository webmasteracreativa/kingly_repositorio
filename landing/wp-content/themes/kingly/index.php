<?php get_header(); ?>

<section class="banner mb-md-5">
	<div class="container">
		<div class="row">
			<div class="col-12 d-block d-lg-none text-center">
				<img src="<?php echo get_template_directory_uri(); ?>/img/mb-banner.png" class="img-fluid d-inline-block ">
			</div>
			<div class="col-lg-3 d-flex flex-column justify-content-center p-md-0">
				<h3 class="text-center">
					¿Quieres tener una familia real? <span> Escribenos</span>
				</h3>
				<?php
					    	// Argumentos para una busqueda de post type
				$args = array(
								'post_type' => 'code', // Nombre del post type
								'order' => 'ASC'
							);
				$codes = new WP_Query($args);
				if ($codes->posts):
					      // Foreach para recorrer el resultado de la busqueda
					foreach ($codes->posts as $code):
						$code = $code->code;
						?>
						<?php echo $code?>
						<?php
					endforeach;
				endif; 
				?>					
			</div>
			<div class="col-lg-9 d-flex aling-items-center justify-content-center justify-content-lg-end">
				<div class="align-self-md-end text-banner">
					<h3>Descarga gratis Ebook <br><span>"Bulldog Francés: Todo sobre esta raza"</span></h3>
					<a href="<?php echo get_template_directory_uri(); ?>/pdf/Book_Kingly.pdf" class="button btn-banner" target="_blank">Descarga</a>
				</div>
			</div>
		</div>
	</div>
</section>
<hr>
<section class="items py-5">
	<div class="container pb-md-5">
		<div class="row">
			<div class="col-12 text-center pb-5">
				<h2 class="under-line d-inline-block">
					¿Por qué un bulldog francés?
				</h2>
			</div>
			<div class="col-md-4 d-flex flex-column justify-content-center item pt-5">
				<div class="text-center">
					<img src="<?php echo get_template_directory_uri(); ?>/img/casa.png" alt="" class="img-fluid d-inline-block">
				</div>
				<h2 class="pb-5">01.</h2>
				<div class="text">
					<h2 class="title">Hogar</h2>
					<p class="pl-5">El bulldog francés es considerado uno de los perros más hogareños.  Se adapta fácilmente. Es dócil, tranquilo, alegre y cariñoso. Ideal para estar en casa.</p>
				</div>
				<!-- <a href="" class="align-self-end button">Información</a> -->
			</div>
			<div class="col-md-4 d-flex flex-column justify-content-center item pt-5">
				<div class="text-center">
					<img src="<?php echo get_template_directory_uri(); ?>/img/hueso.png" alt="" class="img-fluid d-inline-block">
				</div>
				<h2 class="pb-5">02.</h2>
				<div class="text">
					<h2 class="title">Juguetona</h2>
					<p class="pl-5">La diversión es su parte favorita del día. Suele ser amigable, sociable y activo. Ama las actividades al aire libre.</p>
				</div>
				<!-- <a href="" class="align-self-end button">Información</a> -->
			</div>
			<div class="col-md-4 d-flex flex-column justify-content-center item pt-5">
				<div class="text-center">
					<img src="<?php echo get_template_directory_uri(); ?>/img/trofeo.png" alt="" class="img-fluid d-inline-block">
				</div>
				<h2 class="pb-5">03.</h2>
				<div class="text">
					<h2 class="title">Mejor amigo</h2>
					<p class="pl-5">Es el compañero ideal para los niños, las parejas y las familias. Le encanta compartir su corazón; por eso, contar con su compañía y cariño es todo un placer.</p>
				</div>
				<!-- <a href="" class="align-self-end button">Información</a> -->
			</div>
		</div>
	</div>
</section>
<section class="banner-secundario container-fluid pr-0 py-5">
	<div class="row">
		<div class="offset-md-2 col-md-4 d-flex flex-column justify-content-center">
			<h2 class="pb-4 align-self-start under-line">Con un bulldog francés…</h2>
			<p class="pl-md-5 pt-4">Compartirás momentos únicos y conocerás el verdadero significado del amor. Tus días se pintarán de nuevos colores y tus tardes jamás serán iguales. Tu vida, en pocas palabras, recibirá una carga significativa de juegos y ternura. Un bulldog francés es tu verdadero cómplice.</p>
		</div>
		<div class="col-md-6">
			<img src="<?php echo get_template_directory_uri(); ?>/img/perro.png" alt="" class="img-fluid">
		</div>
	</div>
</section>
<section class="video pt-5">
	<div class="container">
		<div class="row justify-content-center">
			<h2 class="pb-4 align-self-start under-line mb-5">Características de la raza Bulldog Francés.</h2>
		</div>
		<div class="row">
			<div class="col-12 ">
				<div class="video-responsive">
					<iframe src="https://www.youtube.com/embed/hki6MOlVv8k" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
				<div class="text-center">
					<a href="#top" class="top d-block"><img src="<?php echo get_template_directory_uri(); ?>/img/top.png" alt="" class=" img-fluid d-inline-block"></a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
