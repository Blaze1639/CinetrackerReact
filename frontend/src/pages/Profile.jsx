import '../styles/profile.css'
import { useState, useEffect } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import Navbar from '../components/Navbar'
import { useAuth } from '../context/AuthContext'
import { useApi } from '../services/api'

export default function Profile() {
  const { logout } = useAuth()
  const navigate = useNavigate()
  const api = useApi()
  const [profile, setProfile] = useState(null)
  const [alert, setAlert] = useState(null)
  const [notifications, setNotifications] = useState([])
  const [suggestion, setSuggestion] = useState({type_message:'',message:''})
  const [newActu, setNewActu] = useState({titre:'',contenu:''})

  useEffect(() => {
    api.auth.profile().then(d => {
      if(d.success){ setProfile(d); if(d.role==='admin') fetchNotifs() }
    })
  }, [])

  useEffect(() => { if(alert){const t=setTimeout(()=>setAlert(null),5000);return()=>clearTimeout(t)} },[alert])

  const fetchNotifs = () => {
    api.notifications.getAll().then(d => { if(d.success) setNotifications(d.notifications||[]) })
  }

  const handleDelete = async () => {
    if (!confirm(`⚠️ ATTENTION ⚠️\n\nÊtes-vous VRAIMENT sûr de vouloir supprimer votre compte ?\n\n- ${profile.total_films} films vus\n- ${profile.total_series} séries vues\n- ${profile.total_a_voir} médias à voir\n\nCette action est DÉFINITIVE et IRRÉVERSIBLE !`)) return
    if (!confirm('⚠️ DERNIÈRE CONFIRMATION ⚠️\n\nToutes vos données seront DÉFINITIVEMENT supprimées.\nIl n\'y aura AUCUN moyen de les récupérer.\n\nVoulez-vous vraiment continuer ?')) return
    const data = await api.auth.delete()
    if(data.success){logout();navigate('/')}
    else setAlert({type:'error',message:'Erreur lors de la suppression.'})
  }

  const handleSuggestion = async (e) => {
    e.preventDefault()
    if (!suggestion.type_message) return setAlert({type:'error',message:'❌ Veuillez sélectionner un type de message'})
    if (suggestion.message.trim().length < 10) return setAlert({type:'error',message:'❌ Votre message doit contenir au moins 10 caractères'})
    const data = await api.notifications.send(suggestion)
    console.log('env_notif response:', data)
    if(!data.success) return setAlert({type:'error',message:'❌ '+(data.error||'Erreur inconnue')})
    setSuggestion({type_message:'',message:''})
    setAlert({type:'success',message:'✅ Votre message a bien été envoyé aux administrateurs !'})
  }

  const handleAddActu = async (e) => {
    e.preventDefault()
    const data = await api.actualites.add(newActu)
    if(data.success){setNewActu({titre:'',contenu:''});setAlert({type:'success',message:'✅ Actualité publiée avec succès !'})}
    else setAlert({type:'error',message:'❌ Erreur lors de l\'ajout.'})
  }

  const handleMarkRead = async (id) => {
    await api.notifications.markRead(id)
    setNotifications(prev=>prev.map(n=>n.id===id?{...n,status:'lu'}:n))
    setAlert({type:'success',message:'✅ Notification marquée comme lue'})
  }

  const handleDeleteNotif = async (id) => {
    if (!confirm('Supprimer cette notification ?')) return
    await api.notifications.remove(id)
    setNotifications(prev=>prev.filter(n=>n.id!==id))
    setAlert({type:'success',message:'✅ Notification supprimée'})
  }

  if (!profile) return <div className="loading">Chargement...</div>

  const initial = (profile.username||'U')[0].toUpperCase()
  const isAdmin = profile.role === 'admin'
  const unreadCount = notifications.filter(n=>n.status==='non_lu').length

  return (
    <>
      <Navbar />
      <div className="profile-container">
        {alert && <div className={`alert alert-${alert.type==='success'?'success':'error'}`} style={{cursor:'pointer'}} onClick={()=>setAlert(null)}>{alert.message}</div>}
        <Link to="/accueil" className="back-link">← Retour à l'accueil</Link>
        <div className="profile-header"><h1>Mon Profil</h1><p>Gérez vos informations et paramètres</p></div>

        <div className="profile-section">
          <div className="avatar-section">
            <div className="avatar">{initial}</div>
            <div className="avatar-name">
              {profile.username}
              {isAdmin && <span style={{background:'#f59e0b',color:'white',padding:'2px 8px',borderRadius:4,fontSize:11,marginLeft:8}}>ADMIN</span>}
            </div>
          </div>
          <div className="quick-stats">
            <div className="stat-box"><span className="stat-number" id="films-count">{profile.total_films||0}</span><span className="stat-label">Films vus</span></div>
            <div className="stat-box"><span className="stat-number" id="series-count">{profile.total_series||0}</span><span className="stat-label">Séries vues</span></div>
            <div className="stat-box"><span className="stat-number" id="a-voir-count">{profile.total_a_voir||0}</span><span className="stat-label">À voir</span></div>
          </div>
        </div>

        <div className="profile-section">
          <h3 className="section-title">Informations du compte</h3>
          <div className="form-group"><label className="form-label">Pseudo</label><input className="form-input" value={profile.username} readOnly /></div>
          <div className="form-group"><label className="form-label">Email</label><input className="form-input" value={profile.email} readOnly /></div>
        </div>

        {isAdmin && (
          <div className="profile-section">
            <h3 className="section-title" style={{color:'red'}}>Ajouter une actualité</h3>
            <form onSubmit={handleAddActu}>
              <div className="form-group"><label className="form-label" style={{color:'red'}}>Titre</label><input className="form-input" value={newActu.titre} onChange={e=>setNewActu(p=>({...p,titre:e.target.value}))} placeholder="Ex: Nouvelle fonctionnalité !" required /></div>
              <div className="form-group"><label className="form-label" style={{color:'red'}}>Contenu</label><textarea className="form-input" rows="5" value={newActu.contenu} onChange={e=>setNewActu(p=>({...p,contenu:e.target.value}))} placeholder="Décrivez l'actualité..." required /></div>
              <button type="submit" className="btn-primary" style={{background:'red'}}>Publier l'actualité</button>
            </form>
          </div>
        )}

        {isAdmin && (
          <div className="profile-section" style={{background:'black'}}>
            <h3 className="section-title" style={{color:'red'}}>
              Panneau d'administration
              {unreadCount > 0 && <span style={{background:'#ff0200',color:'white',padding:'4px 12px',borderRadius:20,fontSize:12,marginLeft:10}}>{unreadCount} non {unreadCount>1?'lues':'lue'}</span>}
            </h3>
            <p style={{color:'white',fontSize:14,marginBottom:20}}>Vous avez accès aux notifications envoyées par les utilisateurs</p>
            {notifications.length===0
              ? <div style={{textAlign:'center',padding:40,color:'#999'}}><p>Aucune notification pour le moment</p></div>
              : <div style={{maxHeight:600,overflowY:'auto'}}>
                {notifications.map(n=>(
                  <div key={n.id} style={{background:n.status==='non_lu'?'black':'white',border:'1px solid #e5e7eb',borderRadius:8,padding:15,marginBottom:15}}>
                    <div style={{display:'flex',justifyContent:'space-between',marginBottom:10}}>
                      <div style={{display:'flex',gap:5}}>
                        <span style={{background:'#ff100e',color:'white',padding:'4px 10px',borderRadius:4,fontSize:11,fontWeight:'bold'}}>{n.type_message?.toUpperCase()}</span>
                        {n.status==='non_lu'&&<span style={{background:'#ff100e',color:'white',padding:'4px 10px',borderRadius:4,fontSize:11,fontWeight:'bold',marginLeft:5}}>NOUVEAU</span>}
                      </div>
                      <span style={{color:'#6b7280',fontSize:12}}>{new Date(n.created_at).toLocaleDateString('fr-FR')} à {new Date(n.created_at).toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'})}</span>
                    </div>
                    <div style={{marginBottom:10}}><strong style={{color:n.status==='non_lu'?'#fff':'#000'}}>{n.sender_username}</strong><span style={{color:'#6b7280',fontSize:13,marginLeft:10}}>({n.sender_email})</span></div>
                    <div style={{background:'white',padding:12,borderRadius:6,color:'#374151',lineHeight:1.6,marginBottom:10}}>{n.message}</div>
                    <div style={{display:'flex',gap:10}}>
                      {n.status==='non_lu'&&<button onClick={()=>handleMarkRead(n.id)} style={{background:'#ff100e',color:'white',border:'none',padding:'6px 12px',borderRadius:4,cursor:'pointer',fontSize:13}}>✓ Marquer comme lu</button>}
                      <button onClick={()=>handleDeleteNotif(n.id)} style={{background:'#ff100e',color:'white',border:'none',padding:'6px 12px',borderRadius:4,cursor:'pointer',fontSize:13}}>Supprimer</button>
                    </div>
                  </div>
                ))}
              </div>
            }
          </div>
        )}

        <div className="profile-section">
          <h3 className="section-title">Envoyer une suggestion</h3>
          <p style={{color:'#999',fontSize:14,marginBottom:20}}>Vous avez une idée d'amélioration ou un problème à signaler ?</p>
          <form onSubmit={handleSuggestion}>
            <div className="form-group">
              <label className="form-label">Type de message</label>
              <select className="form-input" value={suggestion.type_message} onChange={e=>setSuggestion(p=>({...p,type_message:e.target.value}))} required>
                <option value="">-- Choisir un type --</option>
                <option value="suggestion">Suggestion</option>
                <option value="bug">Signaler un bug</option>
                <option value="question">Question</option>
                <option value="autre">📝 Autre</option>
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Votre message</label>
              <textarea className="form-input" rows="5" value={suggestion.message} onChange={e=>setSuggestion(p=>({...p,message:e.target.value}))} placeholder="Décrivez votre suggestion ou votre problème..." required />
            </div>
            <button type="submit" className="btn-primary">📤 Envoyer aux administrateurs</button>
          </form>
        </div>

        <div className="profile-section">
          <h3 className="section-title">Supprimer le compte</h3>
          <div className="danger-zone">
            <h4>Attention : Action irréversible</h4>
            <p>La suppression de votre compte est définitive. Toutes vos données seront supprimées :</p>
            <ul style={{color:'#999',fontSize:13,margin:'15px 0',paddingLeft:20}}>
              <li>{profile.total_films||0} films vus</li>
              <li>{profile.total_series||0} séries vues</li>
              <li>{profile.total_a_voir||0} films/séries dans votre liste "à voir"</li>
              <li>Tous vos commentaires et notes</li>
              <li>Votre compte utilisateur</li>
            </ul>
            <p style={{color:'#999',fontSize:13,marginBottom:20}}><strong>Ces données ne pourront jamais être récupérées.</strong></p>
            <button className="btn-danger" onClick={handleDelete}>Supprimer mon compte</button>
          </div>
        </div>
      </div>
    </>
  )
}
