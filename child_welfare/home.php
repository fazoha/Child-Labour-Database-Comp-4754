<?php
require 'includes/header.php';
?>

<h1 class="mb-4">Child Welfare System Dashboard</h1>

<p class="lead">
  This dashboard lets you browse core entities (districts, schools, children, NGOs,
  labour types, crises) and manage the link tables that represent relationships.
</p>

<div class="row g-3 mb-4">

<!-- Core data tables -->
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Core Data</h5>
        <p class="card-text">View and manage main tables with full CRUD + search.</p>
        <ul class="list-unstyled mb-3">
          <li><a href="index.php?table=district">Districts</a></li>
          <li><a href="index.php?table=school">Schools</a></li>
          <li><a href="index.php?table=child">Children</a></li>
          <li><a href="index.php?table=ngo">NGOs</a></li>
          <li><a href="index.php?table=labour">Labour Types</a></li>
          <li><a href="index.php?table=local_crisis">Local Crises</a></li>
        </ul>
      </div>
    </div>
  </div>
 <!-- Links to each core table -->
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Relationships / Link Tables</h5>
        <p class="card-text">
          Use these pages to create and delete relationships between records.
        </p>

        <!-- Links to pages that manage relationships -->
        <ul class="list-unstyled mb-3">
          <li><a href="links_crisis_effects.php">Crisis <> District (crisis_effects)</a></li>
          <li><a href="links_ngo_operates_in.php">NGO <> District (ngo_operates_in)</a></li>
          <li><a href="links_child_works_in.php">Child <> Labour (child_works_in)</a></li>
          <li><a href="links_support.php">NGO <> School (support)</a></li>
        </ul>
      </div>
    </div>
  </div>

   <!-- Usage tips -->
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Usage Notes</h5>
        <ul>
          <li>Use the navigation bar to jump directly to any table.</li>
          <li>Listing pages support search, create, edit, and delete.</li>
          <li>
            If a delete would break foreign-key constraints, the system warns you
            and keeps the data safe.
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php
require 'includes/footer.php';
