
</div>
<script>
function calculateForm1(){
 const a=+document.getElementById('with_ring').value||0;
 const b=+document.getElementById('without_ring').value||0;
 document.getElementById('total_can').value=a+b;
}
function calculateForm2(){
 const ids=['new_handle','new_bottom_ring','new_bottom_dish','repairing','buffing_can','cleaning_can'];
 let total=0;
 ids.forEach(id=>total += (+document.getElementById(id).value||0));
 document.getElementById('total_can').value=total;
}
</script>
</body></html>
